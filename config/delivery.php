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
    // West Bank Cities - 19 ILS
    'jericho' => 19,
    'hebron' => 19,
    'bethlehem' => 19,
    'jenin' => 19,
    'ramallah' => 19,
    'salfit' => 19,
    'east jerusalem (west bank)' => 19,
    'tubas' => 19,
    'tulkarm' => 19,
    'qalqilyah' => 19,
    'nablus' => 19,

    // Jerusalem (Inner) - 30 ILS
    'jerusalem (القدس الداخل)' => 30,

    // Abo Gosh + Ein Nakoba - 45 ILS
    'abo gosh + ein nakoba' => 45,

    // Israel (مناطق الداخل) - 70 ILS
    'israel (مناطق الداخل)' => 70,
    'golan + gajar' => 70,

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

    'default_price' => 19,
];
