<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class RecommendationService
{
    // Weights for the scoring formula
    const WEIGHT_RATING      = 0.40;
    const WEIGHT_COMPLETIONS = 0.30;
    const WEIGHT_FAVORITES   = 0.15;
    const WEIGHT_AVAILABILITY = 0.10;

    // Newcomer boost: +1.0 for services ≤ 14 days old
    const NEWCOMER_DAYS  = 14;
    const NEWCOMER_BOOST = 1.0;

    // Age decay: -5% per 30-day period after the newcomer window expires
    const DECAY_START_DAYS = 14;
    const DECAY_RATE       = 0.05; // 5% per period
    const DECAY_PERIOD_DAYS = 30;

    /**
     * Apply a ORDER BY recommendation score to a StudentService query builder.
     *
     * Compatible with both Eloquent and raw DB query builders.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function applyToQuery($query): mixed
    {
        $table = 'h2u_student_services';

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

        $w_rating      = self::WEIGHT_RATING;
        $w_completions = self::WEIGHT_COMPLETIONS;
        $w_favorites   = self::WEIGHT_FAVORITES;
        $w_avail       = self::WEIGHT_AVAILABILITY;
        $newcomerDays  = self::NEWCOMER_DAYS;
        $newcomerBoost = self::NEWCOMER_BOOST;
        $decayStart    = self::DECAY_START_DAYS;
        $decayRate     = self::DECAY_RATE;
        $decayPeriod   = self::DECAY_PERIOD_DAYS;

        // Build the full score expression:
        //   base_score = (rating_weight × avg_rating)
        //              + (completions_weight × completed_count)
        //              + (favorites_weight × favorites_count)
        //              + (availability_weight × is_available)
        //   + newcomer_boost IF age in days ≤ NEWCOMER_DAYS
        //   - decay_rate × FLOOR(MAX(age_days - DECAY_START, 0) / DECAY_PERIOD) × base_score  (capped ≥ 0)
        $scoreExpr = "
            GREATEST(0, (
                ({$w_rating}      * ({$ratingSubquery}))
              + ({$w_completions} * ({$completionsSubquery}))
              + ({$w_favorites}   * ({$favoritesSubquery}))
              + ({$w_avail}       * CASE WHEN {$table}.hss_status = 'available' THEN 1 ELSE 0 END)
              + CASE
                    WHEN EXTRACT(DAY FROM (NOW() - {$table}.created_at)) <= {$newcomerDays}
                    THEN {$newcomerBoost}
                    ELSE 0
                END
            ) * (1 - ({$decayRate} * FLOOR(
                GREATEST(0, EXTRACT(DAY FROM (NOW() - {$table}.created_at)) - {$decayStart})
                / {$decayPeriod}
            )))
        )";

        $scoreBindings = array_merge(
            $ratingSubqueryQuery->getBindings(),
            $completionsSubqueryQuery->getBindings(),
            $favoritesSubqueryQuery->getBindings()
        );

        return $query->orderByRaw("{$scoreExpr} DESC", $scoreBindings);
    }

    /**
     * Convenience: get the raw SQL for the score expression (for debugging/reporting).
     */
    public function getScoreExpression(): string
    {
        // Re-uses applyToQuery logic — not meant for direct production use,
        // only as a debug/documentation helper.
        return 'See applyToQuery() for the full SQL expression.';
    }
}
