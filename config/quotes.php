<?php

return [
    'currency_symbol' => '$',
    'currency_code' => 'USD',

    // Default "valid until" = today + this many days.
    'validity_days' => (int) env('QUOTE_VALIDITY_DAYS', 14),

    // Children are priced at this share of the adult package price.
    'child_rate' => (float) env('QUOTE_CHILD_RATE', 0.7),

    // Where website quote requests are emailed.
    'office_email' => env('QUOTES_OFFICE_EMAIL', 'hello@suniexpedition.co.tz'),

    'instant_note' => 'This is an instant estimate based on our standard package price. Final prices depend on travel dates, accommodation availability and park fees at the time of booking; we will confirm everything with you before you pay anything.',

    'company' => [
        'name' => 'Suni Expedition',
        'address' => 'Arusha, Tanzania',
        'phone' => '+255 754 000 111',
        'email' => 'hello@suniexpedition.co.tz',
        'website' => 'suniexpedition.co.tz',
    ],

    'terms' => [
        'Prices are per the itemised list above and are valid until the date shown.',
        'A 30% deposit confirms your booking; the balance is due 60 days before travel.',
        'Park and conservation fees are set by the Tanzanian authorities and may change before travel.',
        'International flights, visas, travel insurance and tips are not included unless listed.',
    ],
];
