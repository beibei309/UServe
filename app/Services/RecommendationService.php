<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RecommendationService
{
    /**
     * Apply a ORDER BY recommendation score to a StudentService query builder.
     *
     * All tuning values are read from config/recommendation.php, which reads
     * from .env. Change values in .env and run `php artisan config:clear`.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyToQuery($query): mixed
    {
        $table = 'h2u_student_services';

        // ── Weights ────────────────────────────────────────────────────────
        $w_rating      = config('recommendation.weights.rating',       0.40);
        $w_completions = config('recommendation.weights.completions',   0.30);
        $w_favorites   = config('recommendation.weights.favorites',     0.15);
        $w_avail       = config('recommendation.weights.availability',  0.10);

        // ── New-seller launch boost ────────────────────────────────────────
        $newSellerMaxCompletedOrders = config('recommendation.new_seller.max_completed_orders', 5);
        $newSellerMaxAccountAgeDays  = config('recommendation.new_seller.max_account_age_days', 30);
        $newSellerStrongDays         = config('recommendation.new_seller.strong_boost_days',    3);
        $newSellerSoftDays           = config('recommendation.new_seller.soft_boost_days',      5);
        $newSellerStrongBoost        = config('recommendation.new_seller.strong_boost',         0.80);
        $newSellerSoftBoost          = config('recommendation.new_seller.soft_boost',           0.30);

        // ── Existing-seller launch boost ───────────────────────────────────
        $existingSellerBoostDays = config('recommendation.existing_seller.boost_days', 1);
        $existingSellerBoost     = config('recommendation.existing_seller.boost',      0.20);

        // ── Hard priority bucket: show fresh listings first ────────────────
        $newListingFirstDays = config('recommendation.priority.new_listing_first_days', 3);

        // ── Age decay ─────────────────────────────────────────────────────
        $decayStart  = config('recommendation.decay.start_days',   14);
        $decayRate   = config('recommendation.decay.rate',          0.05);
        $decayPeriod = config('recommendation.decay.period_days',   30);

        // --- Sub-query: Average rating for this service ---
        $ratingSubqueryQuery = DB::table('h2u_reviews')
            ->selectRaw('COALESCE(AVG(hr_rating), 0)')
            ->whereColumn('hr_student_service_id', "{$table}.hss_id");

        $ratingSubquery = $ratingSubqueryQuery->toSql();

        // --- Sub-query: Count of completed bookings for this service ---
        $completionsSubqueryQuery = DB::table('h2u_service_requests')
            ->selectRaw('COUNT(*)')
            ->whereColumn('hsr_student_service_id', "{$table}.hss_id")
            ->where('hsr_status', 'completed');

        $completionsSubquery = $completionsSubqueryQuery->toSql();

        // --- Sub-query: Count of favorites for this service ---
        $favoritesSubqueryQuery = DB::table('h2u_favorites')
            ->selectRaw('COUNT(*)')
            ->whereColumn('hf_service_id', "{$table}.hss_id");

        $favoritesSubquery = $favoritesSubqueryQuery->toSql();

        // --- Sub-query: Seller completed orders across all their services ---
        $sellerCompletedOrdersSubqueryQuery = DB::table('h2u_service_requests as hsr2')
            ->join('h2u_student_services as hss2', 'hss2.hss_id', '=', 'hsr2.hsr_student_service_id')
            ->selectRaw('COUNT(*)')
            ->whereColumn('hss2.hss_user_id', "{$table}.hss_user_id")
            ->where('hsr2.hsr_status', 'completed');

        $sellerCompletedOrdersSubquery = $sellerCompletedOrdersSubqueryQuery->toSql();

        // --- Sub-query: Seller account age in days ---
        $sellerAccountAgeDaysSubqueryQuery = DB::table('h2u_users as hu')
            ->selectRaw('COALESCE(EXTRACT(DAY FROM (NOW() - hu.created_at)), 9999)')
            ->whereColumn('hu.hu_id', "{$table}.hss_user_id")
            ->limit(1);

        $sellerAccountAgeDaysSubquery = $sellerAccountAgeDaysSubqueryQuery->toSql();

        // ── Build SQL expressions ──────────────────────────────────────────
        $serviceAgeExpr       = "EXTRACT(DAY FROM (NOW() - {$table}.created_at))";
        $sellerCompletedExpr  = "({$sellerCompletedOrdersSubquery})";
        $sellerAccountAgeExpr = "({$sellerAccountAgeDaysSubquery})";

        $isNewSellerExpr = "(
            ({$sellerCompletedExpr} < {$newSellerMaxCompletedOrders})
            OR ({$sellerAccountAgeExpr} < {$newSellerMaxAccountAgeDays})
        )";

        $launchBoostExpr = "
            CASE
                WHEN {$isNewSellerExpr} AND {$serviceAgeExpr} <= {$newSellerStrongDays}
                    THEN {$newSellerStrongBoost}
                WHEN {$isNewSellerExpr} AND {$serviceAgeExpr} <= {$newSellerSoftDays}
                    THEN {$newSellerSoftBoost}
                WHEN NOT ({$isNewSellerExpr}) AND {$serviceAgeExpr} <= {$existingSellerBoostDays}
                    THEN {$existingSellerBoost}
                ELSE 0
            END
        ";

        $newListingPriorityExpr = "
            CASE
                WHEN {$serviceAgeExpr} <= {$newListingFirstDays} THEN 1
                ELSE 0
            END
        ";

        $newListingCreatedAtExpr = "
            CASE
                WHEN {$serviceAgeExpr} <= {$newListingFirstDays} THEN {$table}.created_at
                ELSE NULL
            END
        ";

        // Build the full score expression:
        //   base_score = (rating_weight × avg_rating)
        //              + (completions_weight × completed_count)
        //              + (favorites_weight × favorites_count)
        //              + (availability_weight × is_available)
        //              + launch_boost (tiered by seller freshness + service age)
        //   final_score = base_score × (1 - decay_rate × periods_after_decay_start)
        $scoreExpr = "
            GREATEST(0, (
                ({$w_rating}      * ({$ratingSubquery}))
              + ({$w_completions} * ({$completionsSubquery}))
              + ({$w_favorites}   * ({$favoritesSubquery}))
              + ({$w_avail}       * CASE WHEN {$table}.hss_status = 'available' THEN 1 ELSE 0 END)
              + ({$launchBoostExpr})
            ) * (1 - ({$decayRate} * FLOOR(
                GREATEST(0, {$serviceAgeExpr} - {$decayStart})
                / {$decayPeriod}
            )))
        )";

        // $isNewSellerExpr is embedded 3 times in $launchBoostExpr (one per CASE WHEN branch).
        // Each embedding contains $sellerCompletedOrdersSubquery which has 1 bound `?` parameter.
        // The bindings must be supplied once per embedding, in SQL order.
        $scoreBindings = array_merge(
            $ratingSubqueryQuery->getBindings(),                 // 0 bindings (uses whereColumn)
            $completionsSubqueryQuery->getBindings(),            // 1 binding: 'completed'
            $favoritesSubqueryQuery->getBindings(),              // 0 bindings (uses whereColumn)
            // CASE WHEN 1 — new seller strong boost check
            $sellerCompletedOrdersSubqueryQuery->getBindings(),  // 1 binding: 'completed'
            $sellerAccountAgeDaysSubqueryQuery->getBindings(),   // 0 bindings (uses whereColumn)
            // CASE WHEN 2 — new seller soft boost check
            $sellerCompletedOrdersSubqueryQuery->getBindings(),  // 1 binding: 'completed'
            $sellerAccountAgeDaysSubqueryQuery->getBindings(),   // 0 bindings
            // CASE WHEN NOT — existing seller boost check
            $sellerCompletedOrdersSubqueryQuery->getBindings(),  // 1 binding: 'completed'
            $sellerAccountAgeDaysSubqueryQuery->getBindings(),   // 0 bindings
        );

        return $query
            ->orderByRaw("{$newListingPriorityExpr} DESC")
            ->orderByRaw("{$newListingCreatedAtExpr} DESC")
            ->orderByRaw("{$scoreExpr} DESC", $scoreBindings)
            ->orderBy("{$table}.created_at", 'DESC');
    }

    /**
     * Convenience: get the raw SQL for the score expression (for debugging/reporting).
     */
    public function getScoreExpression(): string
    {
        return 'See applyToQuery() for the full SQL expression.';
    }
}
