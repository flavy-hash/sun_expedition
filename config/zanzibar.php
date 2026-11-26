<?php

/*
 * Zanzibar trip styles, keyed by the package's "Trip style" field (stored in tours.difficulty).
 * The Zanzibar menu shows one link per style that has packages, in this order, linking to /zanzibar?tier=<slug>.
 * Styles typed in the admin that aren't listed here still appear, after these, under their own name.
 */

return [
    'styles' => [
        'Relaxed' => [
            'label' => 'Beach holidays',
            'text' => 'Stay at one beach resort and fill the days as you like: Safari Blue, Stone Town, Jozani Forest or nothing at all.',
            'image' => 'images/tours/nungwi-beach.jpg',
        ],
        'Safari & beach' => [
            'label' => 'Safari & Zanzibar',
            'text' => 'A private safari through Tarangire, the Serengeti and Ngorongoro Crater, then a flight to the beach.',
            'image' => 'images/tours/paje-beach.jpg',
        ],
        'Honeymoon' => [
            'label' => 'Honeymoons',
            'text' => 'A private safari for two with slow days built in, finishing with sunsets on the Indian Ocean.',
            'image' => 'images/tours/zanzibar-honeymoon.jpg',
        ],
        'Cultural' => [
            'label' => 'Stone Town & culture',
            'text' => 'Day trips through the old town, the spice farms and the stories behind them.',
            'image' => 'images/tours/stone-town.jpg',
        ],
        'Island escape' => [
            'label' => 'Mafia & Pemba islands',
            'text' => "Whale sharks, marine parks and world-class diving on Tanzania's quieter islands.",
            'image' => 'images/tours/mafia-whale-shark.jpg',
        ],
    ],
];
