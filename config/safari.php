<?php

/*
 * Circuit pages (/northern-circuit, /southern-circuit) can be narrowed by trip length (?length=short).
 * A circuit menu whose "children" is "circuit-lengths:{circuit}" shows one link per length that has safaris,
 * in this order. Bounds are inclusive; a null max means "and longer".
 */

return [
    'lengths' => [
        'short' => [
            'label' => 'Short safaris (up to 5 days)',
            'heading' => 'Short safaris',
            'text' => 'Four or five days for the highlights: the Great Migration, the calving plains or Tarangire and the Crater.',
            'image' => 'images/safaris/mara-river-crossing.jpg',
            'min' => 1,
            'max' => 5,
        ],
        'week' => [
            'label' => 'One-week safaris (6–8 days)',
            'heading' => 'One-week safaris',
            'text' => 'Every major park at an easy pace, with time for a Maasai village, Olduvai Gorge or a second day in the Serengeti.',
            'image' => 'images/safaris/ngorongoro-crater.jpg',
            'min' => 6,
            'max' => 8,
        ],
        'long' => [
            'label' => 'Long safaris (9+ days)',
            'heading' => 'Long safaris',
            'text' => 'The full Northern Circuit, from Tarangire to the Mara River in the far north, with nothing rushed.',
            'image' => 'images/safaris/mara-river-herds.jpg',
            'min' => 9,
            'max' => null,
        ],
    ],
];
