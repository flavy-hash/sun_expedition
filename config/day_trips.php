<?php

/*
 * Day trips are grouped by where they start. Each trip stores one of these keys in tours.departs_from;
 * the Day Trips menu shows one link per group that has trips, in this order (/day-trips?from=<key>).
 */

return [
    'departures' => [
        'arusha' => [
            'label' => 'From Arusha',
            'heading' => 'Day trips from Arusha',
            'text' => 'National park walks, Tarangire, horse riding and the best of Arusha\'s markets, back at your hotel by evening.',
            'image' => 'images/tours/tarangire-manyara.jpg',
        ],
        'moshi' => [
            'label' => 'From Moshi & Kilimanjaro',
            'heading' => 'Day trips from Moshi',
            'text' => 'Waterfalls, hot springs, crater lakes and a taste of Kilimanjaro itself. Ideal before or after a climb.',
            'image' => 'images/day-trips/materuni-waterfall.jpg',
        ],
        'overnight' => [
            'label' => 'Overnight adventures',
            'heading' => 'Overnight adventures',
            'text' => 'Short trips with one night away, like the pre-dawn climb of Ol Doinyo Lengai above Lake Natron.',
            'image' => 'images/day-trips/ol-doinyo-lengai.jpg',
        ],
    ],
];
