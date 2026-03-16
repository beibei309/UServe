<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Recommendation Engine — Scoring Weights
    |--------------------------------------------------------------------------
    | Controls how much each factor contributes to a service's score.
    | Must sum to ≤ 1.0. Tune via .env without touching any PHP code.
    |
    | RECOMMEND_WEIGHT_RATING=0.40
    | RECOMMEND_WEIGHT_COMPLETIONS=0.30
    | RECOMMEND_WEIGHT_FAVORITES=0.15
    | RECOMMEND_WEIGHT_AVAILABILITY=0.10
    */
    'weights' => [
        'rating'       => (float) env('RECOMMEND_WEIGHT_RATING',       0.40),
        'completions'  => (float) env('RECOMMEND_WEIGHT_COMPLETIONS',   0.30),
        'favorites'    => (float) env('RECOMMEND_WEIGHT_FAVORITES',     0.15),
        'availability' => (float) env('RECOMMEND_WEIGHT_AVAILABILITY',  0.10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiered Launch Boost — New Seller
    |--------------------------------------------------------------------------
    | A seller is considered "new" if they have fewer than
    | RECOMMEND_NEW_SELLER_MAX_ORDERS completed orders OR their account
    | is younger than RECOMMEND_NEW_SELLER_MAX_AGE_DAYS days.
    |
    | Within the first RECOMMEND_NEW_SELLER_STRONG_BOOST_DAYS days of listing,
    | the service receives RECOMMEND_NEW_SELLER_STRONG_BOOST added to its score.
    | Between that window and RECOMMEND_NEW_SELLER_SOFT_BOOST_DAYS, a softer
    | RECOMMEND_NEW_SELLER_SOFT_BOOST is applied instead.
    |
    | RECOMMEND_NEW_SELLER_MAX_ORDERS=5
    | RECOMMEND_NEW_SELLER_MAX_AGE_DAYS=30
    | RECOMMEND_NEW_SELLER_STRONG_BOOST_DAYS=3
    | RECOMMEND_NEW_SELLER_SOFT_BOOST_DAYS=5
    | RECOMMEND_NEW_SELLER_STRONG_BOOST=0.80
    | RECOMMEND_NEW_SELLER_SOFT_BOOST=0.30
    */
    'new_seller' => [
        'max_completed_orders' => (int)   env('RECOMMEND_NEW_SELLER_MAX_ORDERS',         5),
        'max_account_age_days' => (int)   env('RECOMMEND_NEW_SELLER_MAX_AGE_DAYS',       30),
        'strong_boost_days'    => (int)   env('RECOMMEND_NEW_SELLER_STRONG_BOOST_DAYS',  3),
        'soft_boost_days'      => (int)   env('RECOMMEND_NEW_SELLER_SOFT_BOOST_DAYS',    5),
        'strong_boost'         => (float) env('RECOMMEND_NEW_SELLER_STRONG_BOOST',       0.80),
        'soft_boost'           => (float) env('RECOMMEND_NEW_SELLER_SOFT_BOOST',         0.30),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tiered Launch Boost — Existing Seller
    |--------------------------------------------------------------------------
    | Established sellers still get a light bump on day 1 of a new listing.
    |
    | RECOMMEND_EXISTING_SELLER_BOOST_DAYS=1
    | RECOMMEND_EXISTING_SELLER_BOOST=0.20
    */
    'existing_seller' => [
        'boost_days' => (int)   env('RECOMMEND_EXISTING_SELLER_BOOST_DAYS', 1),
        'boost'      => (float) env('RECOMMEND_EXISTING_SELLER_BOOST',       0.20),
    ],

    /*
    |--------------------------------------------------------------------------
    | Hard Priority Bucket — New Listings First
    |--------------------------------------------------------------------------
    | Services within this age window are always ranked ahead of older listings
    | before score sorting is applied.
    |
    | RECOMMEND_NEW_LISTING_FIRST_DAYS=3
    */
    'priority' => [
        'new_listing_first_days' => (int) env('RECOMMEND_NEW_LISTING_FIRST_DAYS', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Age Decay
    |--------------------------------------------------------------------------
    | After RECOMMEND_DECAY_START_DAYS days, the score decays by
    | RECOMMEND_DECAY_RATE per RECOMMEND_DECAY_PERIOD_DAYS-day period.
    | e.g. defaults → -5% every 30 days, starting at day 14.
    |
    | RECOMMEND_DECAY_START_DAYS=14
    | RECOMMEND_DECAY_RATE=0.05
    | RECOMMEND_DECAY_PERIOD_DAYS=30
    */
    'decay' => [
        'start_days'  => (int)   env('RECOMMEND_DECAY_START_DAYS',   14),
        'rate'        => (float) env('RECOMMEND_DECAY_RATE',          0.05),
        'period_days' => (int)   env('RECOMMEND_DECAY_PERIOD_DAYS',   30),
    ],

];
