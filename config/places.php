<?php

/*
 * Places the itinerary route map knows about. Each day is pinned automatically by matching these keywords
 * (whole words, case-insensitive) in the day title, then the photo caption, then the overnight area.
 * Admins can also pick a place explicitly or enter custom coordinates per day.
 * Coordinates are approximate and only used for the map.
 */

return [
    // Towns & airports
    'arusha' => ['name' => 'Arusha', 'lat' => -3.3869, 'lng' => 36.6830, 'match' => ['arusha']],
    'moshi' => ['name' => 'Moshi', 'lat' => -3.3349, 'lng' => 37.3404, 'match' => ['moshi']],
    'kilimanjaro-airport' => ['name' => 'Kilimanjaro International Airport', 'lat' => -3.4294, 'lng' => 37.0745, 'match' => ['kilimanjaro international airport', 'kilimanjaro airport', 'jro']],
    'karatu' => ['name' => 'Karatu', 'lat' => -3.3390, 'lng' => 35.6700, 'match' => ['karatu']],
    'mto-wa-mbu' => ['name' => 'Mto wa Mbu', 'lat' => -3.3667, 'lng' => 35.8500, 'match' => ['mto wa mbu']],
    'dar-es-salaam' => ['name' => 'Dar es Salaam', 'lat' => -6.7924, 'lng' => 39.2083, 'match' => ['dar es salaam']],
    'iringa' => ['name' => 'Iringa', 'lat' => -7.7700, 'lng' => 35.6900, 'match' => ['iringa']],

    // Northern Circuit parks
    'arusha-national-park' => ['name' => 'Arusha National Park', 'lat' => -3.2400, 'lng' => 36.8800, 'match' => ['arusha national park', 'momella']],
    'tarangire' => ['name' => 'Tarangire National Park', 'lat' => -3.8800, 'lng' => 36.0000, 'match' => ['tarangire']],
    'lake-manyara' => ['name' => 'Lake Manyara National Park', 'lat' => -3.5300, 'lng' => 35.8300, 'match' => ['lake manyara', 'manyara']],
    'ngorongoro' => ['name' => 'Ngorongoro Crater', 'lat' => -3.1750, 'lng' => 35.5878, 'match' => ['ngorongoro', 'crater']],
    'serengeti' => ['name' => 'Central Serengeti (Seronera)', 'lat' => -2.4333, 'lng' => 34.8333, 'match' => ['serengeti', 'seronera']],
    'northern-serengeti' => ['name' => 'Northern Serengeti (Kogatende)', 'lat' => -1.5667, 'lng' => 34.8833, 'match' => ['northern serengeti', 'kogatende', 'mara river']],
    'ndutu' => ['name' => 'Ndutu', 'lat' => -3.0167, 'lng' => 35.0333, 'match' => ['ndutu']],
    'lake-natron' => ['name' => 'Lake Natron', 'lat' => -2.4167, 'lng' => 36.0500, 'match' => ['lake natron', 'natron']],
    'lake-eyasi' => ['name' => 'Lake Eyasi', 'lat' => -3.6000, 'lng' => 35.0500, 'match' => ['lake eyasi', 'eyasi']],

    // Southern Circuit parks
    'ruaha' => ['name' => 'Ruaha National Park', 'lat' => -7.7160, 'lng' => 34.9200, 'match' => ['ruaha']],
    'nyerere' => ['name' => 'Nyerere National Park', 'lat' => -7.5300, 'lng' => 38.2500, 'match' => ['nyerere', 'selous', 'rufiji']],
    'mikumi' => ['name' => 'Mikumi National Park', 'lat' => -7.3500, 'lng' => 37.0000, 'match' => ['mikumi']],

    // Kilimanjaro routes
    'londorossi-gate' => ['name' => 'Londorossi Gate', 'lat' => -3.0540, 'lng' => 37.1800, 'match' => ['londorossi']],
    'machame-gate' => ['name' => 'Machame Gate', 'lat' => -3.1680, 'lng' => 37.2420, 'match' => ['machame gate']],
    'marangu-gate' => ['name' => 'Marangu Gate', 'lat' => -3.2440, 'lng' => 37.5200, 'match' => ['marangu gate']],
    'mweka-gate' => ['name' => 'Mweka Gate', 'lat' => -3.2350, 'lng' => 37.3380, 'match' => ['mweka gate']],
    'mti-mkubwa' => ['name' => 'Mti Mkubwa Camp', 'lat' => -3.0700, 'lng' => 37.2300, 'match' => ['mti mkubwa', 'big tree camp']],
    'machame-camp' => ['name' => 'Machame Camp', 'lat' => -3.1220, 'lng' => 37.2540, 'match' => ['machame camp']],
    'shira-1' => ['name' => 'Shira 1 Camp', 'lat' => -3.0300, 'lng' => 37.2400, 'match' => ['shira 1']],
    'shira-2' => ['name' => 'Shira 2 Camp', 'lat' => -3.0340, 'lng' => 37.2760, 'match' => ['shira 2', 'shira camp', 'shira plateau']],
    'lava-tower' => ['name' => 'Lava Tower', 'lat' => -3.0700, 'lng' => 37.3300, 'match' => ['lava tower']],
    'barranco' => ['name' => 'Barranco Camp', 'lat' => -3.0930, 'lng' => 37.3350, 'match' => ['barranco']],
    'karanga' => ['name' => 'Karanga Camp', 'lat' => -3.0980, 'lng' => 37.3740, 'match' => ['karanga']],
    'barafu' => ['name' => 'Barafu Camp', 'lat' => -3.0900, 'lng' => 37.3900, 'match' => ['barafu']],
    'uhuru-peak' => ['name' => 'Uhuru Peak', 'lat' => -3.0758, 'lng' => 37.3533, 'match' => ['uhuru peak', 'uhuru']],
    'mweka-camp' => ['name' => 'Mweka Camp', 'lat' => -3.1370, 'lng' => 37.3780, 'match' => ['mweka camp']],
    'mandara-hut' => ['name' => 'Mandara Hut', 'lat' => -3.1980, 'lng' => 37.5170, 'match' => ['mandara']],
    'horombo-hut' => ['name' => 'Horombo Hut', 'lat' => -3.1350, 'lng' => 37.4840, 'match' => ['horombo', 'zebra rocks']],
    'kibo-hut' => ['name' => 'Kibo Hut', 'lat' => -3.0950, 'lng' => 37.3960, 'match' => ['kibo hut']],

    // Zanzibar
    'zanzibar-airport' => ['name' => 'Zanzibar Airport', 'lat' => -6.2220, 'lng' => 39.2249, 'match' => ['zanzibar airport', 'znz']],
    'stone-town' => ['name' => 'Stone Town', 'lat' => -6.1630, 'lng' => 39.1890, 'match' => ['stone town', 'zanzibar city']],
    'spice-farm' => ['name' => 'Spice farms (Kizimbani)', 'lat' => -6.1000, 'lng' => 39.2600, 'match' => ['spice farm', 'spice tour', 'kizimbani']],
    'prison-island' => ['name' => 'Prison Island', 'lat' => -6.1110, 'lng' => 39.1670, 'match' => ['prison island', 'changuu']],
    'nungwi' => ['name' => 'Nungwi', 'lat' => -5.7260, 'lng' => 39.2970, 'match' => ['nungwi']],
    'kendwa' => ['name' => 'Kendwa', 'lat' => -5.7500, 'lng' => 39.2930, 'match' => ['kendwa']],
    'kiwengwa' => ['name' => 'Kiwengwa', 'lat' => -5.9900, 'lng' => 39.3800, 'match' => ['kiwengwa']],
    'mnemba' => ['name' => 'Mnemba Atoll', 'lat' => -5.8190, 'lng' => 39.3830, 'match' => ['mnemba']],
    'paje' => ['name' => 'Paje', 'lat' => -6.2660, 'lng' => 39.5360, 'match' => ['paje']],
    'jozani' => ['name' => 'Jozani Forest', 'lat' => -6.2600, 'lng' => 39.4100, 'match' => ['jozani']],
];
