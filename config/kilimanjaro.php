<?php

/*
 * Routes up Kilimanjaro. Each Kilimanjaro package picks one in the admin (Packages → Kilimanjaro → Route).
 * Routes with 'menu' => true appear in the Kilimanjaro dropdown, in this order, as soon as at least one
 * package uses them; each links to /kilimanjaro?route={key} listing every package on that route.
 */

return [
    'routes' => [
        'machame' => [
            'label' => 'Machame Route',
            'menu' => true,
            'text' => 'The "Whiskey Route": steep, scenic and varied, through five climate zones and over the Barranco Wall.',
        ],
        'lemosho' => [
            'label' => 'Lemosho Route',
            'menu' => true,
            'text' => 'The quiet western approach across the Shira Plateau, with the best acclimatization and scenery.',
        ],
        'rongai' => [
            'label' => 'Rongai Route',
            'menu' => true,
            'text' => 'The only northern approach: drier, quieter and gentler, with a night under the Mawenzi spires.',
        ],
        'marangu' => [
            'label' => 'Marangu Route',
            'menu' => true,
            'text' => 'The "Coca-Cola Route": the gentlest gradient and the only one with mountain huts instead of tents.',
        ],
        'umbwe' => [
            'label' => 'Umbwe Route',
            'menu' => true,
            'text' => 'The steepest, most direct way to the top, for experienced and already-acclimatized hikers.',
        ],
        'northern-circuit' => [
            'label' => 'Northern Circuit Route',
            'menu' => false,
            'text' => 'The longest, quietest route, circling the remote northern slopes of Kibo.',
        ],
    ],
];
