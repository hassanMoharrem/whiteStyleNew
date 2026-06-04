<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Delivery Pricing Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines delivery prices based on city names (English).
    | These prices are used server-side to calculate delivery fees securely.
    |
    */

    'prices' => [
        // West Bank Cities - 25 ILS
        'jericho' => 25,
        'hebron' => 25,
        'bethlehem' => 25,
        'jenin' => 25,
        'ramallah' => 25,
        'salfit' => 25,
        'east jerusalem (west bank)' => 25,
        'tubas' => 25,
        'tulkarm' => 25,
        'qalqilyah' => 25,
        'nablus' => 25,

        // Jerusalem (Inner) - 35 ILS
        'jerusalem (القدس الداخل)' => 35,

        // Abo Gosh + Ein Nakoba - 45 ILS
        'abo gosh + ein nakoba' => 45,

        // Israel (مناطق الداخل) - 80 ILS
        'israel (مناطق الداخل)' => 80,
        'golan + gajar' => 80,

        // Eilat - 110 ILS
        'eilat' => 110,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Delivery Price
    |--------------------------------------------------------------------------
    |
    | This is the fallback price used when a city is not found in the
    | pricing configuration above. Default is set to West Bank pricing.
    |
    */

    'default_price' => 25,
];
