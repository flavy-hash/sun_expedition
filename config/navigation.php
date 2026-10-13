<?php

/*
 * Top navigation. Each item opens a mega dropdown: child links on the left, and a live preview (photo, title,
 * text, call-to-action) that swaps as you hover or focus each child. The item's own title/text/image is the
 * preview shown when the panel opens.
 *
 * "children" is either a list of links, or the name of a source that builds them from the database:
 *   safari-tiers        one link per safari tier in use (Budget, Classic…) → /safari?tier=…
 *   circuit:{name}      every safari in that circuit
 *   category:{name}     every package in that category
 *   journal             the latest published journal posts
 * Static children use: label, path, title, text, cta, image. A child of type "stays" expands into
 * "Where you'll stay" plus the busiest accommodation areas (/accommodations?location=…).
 *
 * "active" lists the URL segments that mark the item as the current section.
 */

return [
    'primary' => [
        [
            'key' => 'safari',
            'label' => 'Safari',
            'path' => '/safari',
            'active' => ['safari'],
            'title' => 'Tanzania Safari Adventures',
            'text' => 'Serengeti, Ngorongoro, Tarangire and Ruaha with Tanzanian guides who build each route around where the animals actually are this week.',
            'cta' => 'Explore safaris',
            'image' => 'images/nav/safari-lion.jpg',
            'all' => 'View all safaris',
            'children' => 'safari-tiers',
        ],
        [
            'key' => 'northern-circuit',
            'label' => 'Northern Circuit',
            'path' => '/northern-circuit',
            'active' => ['northern-circuit'],
            'title' => 'The Northern Circuit',
            'text' => "Serengeti, Ngorongoro Crater, Tarangire and Lake Manyara — Tanzania's classic safari country and home of the Great Migration.",
            'cta' => 'Explore the north',
            'image' => 'images/tours/northern-circuit.jpg',
            'all' => 'View all Northern Circuit',
            'children' => 'circuit:northern',
        ],
        [
            'key' => 'southern-circuit',
            'label' => 'Southern Circuit',
            'path' => '/southern-circuit',
            'active' => ['southern-circuit'],
            'title' => 'The Southern Circuit',
            'text' => "Ruaha and Nyerere — vast, remote parks with big predator numbers and a fraction of the vehicles you'll see up north.",
            'cta' => 'Explore the south',
            'image' => 'images/tours/southern-circuit.jpg',
            'all' => 'View all Southern Circuit',
            'children' => 'circuit:southern',
        ],
        [
            'key' => 'kilimanjaro',
            'label' => 'Kilimanjaro',
            'path' => '/kilimanjaro',
            'active' => ['kilimanjaro'],
            'title' => 'Climb Mount Kilimanjaro',
            'text' => "Guided treks up Africa's highest peak through five climate zones, on the route that fits your time and fitness.",
            'cta' => 'Explore Kilimanjaro',
            'image' => 'images/hero/kilimanjaro.jpg',
            'all' => 'View all Kilimanjaro',
            'children' => 'category:kilimanjaro',
        ],
        [
            'key' => 'zanzibar',
            'label' => 'Zanzibar',
            'path' => '/zanzibar',
            'active' => ['zanzibar'],
            'title' => 'Zanzibar Island Escapes',
            'text' => 'Stone Town history, spice farm tours and beach time on the Indian Ocean coast.',
            'cta' => 'Explore Zanzibar',
            'image' => 'images/hero/zanzibar.jpg',
            'all' => 'View all Zanzibar',
            'children' => 'category:zanzibar',
        ],
        [
            'key' => 'about',
            'label' => 'About',
            'path' => '/about',
            'active' => ['about', 'accommodations', 'reviews', 'faq', 'contact'],
            'title' => 'A Tanzanian Team',
            'text' => 'A Tanzanian-owned team based in Arusha — meet the guides and planners behind every trip.',
            'cta' => 'Read our story',
            'image' => 'images/hero/about.jpg',
            'children' => [
                ['label' => 'About Us', 'path' => '/about', 'title' => 'Our story', 'text' => 'Why we started Suni Expedition, and why every trip is designed and led by our own guides.', 'cta' => 'Read our story', 'image' => 'images/hero/about.jpg'],
                ['type' => 'stays', 'limit' => 3],
                ['label' => 'Reviews', 'path' => '/reviews', 'title' => 'Traveler reviews', 'text' => 'Honest reviews and photos from travelers who went on safari, climbed or relaxed with us.', 'cta' => 'Read the reviews', 'image' => 'images/tours/southern-circuit.jpg'],
                ['label' => 'FAQ', 'path' => '/faq', 'title' => 'Questions, answered', 'text' => 'Visas, vaccinations, tipping, what to pack and the best time to go.', 'cta' => 'Read the FAQ', 'image' => 'images/blog/packing-list.jpg'],
                ['label' => 'Contact', 'path' => '/contact', 'title' => 'Talk to a planner', 'text' => 'Send us your dates and ideas and we reply within one business day.', 'cta' => 'Contact us', 'image' => 'images/hero/home.jpg'],
            ],
        ],
        [
            'key' => 'journal',
            'label' => 'Journal',
            'path' => '/journal',
            'active' => ['journal'],
            'title' => 'Field Notes & Guides',
            'text' => 'Route comparisons, packing lists and honest field notes from our guides.',
            'cta' => 'Read the journal',
            'image' => 'images/blog/great-migration.jpg',
            'all' => 'View all journal',
            'children' => 'journal',
        ],
    ],
];
