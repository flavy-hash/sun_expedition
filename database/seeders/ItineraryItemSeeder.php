<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class ItineraryItemSeeder extends Seeder
{
    public function run(): void
    {
        $itineraries = [
            'lemosho-route' => [
                ['title' => 'Londorossi Gate to Mti Mkubwa Camp', 'description' => 'Drive to Londorossi Gate for registration, then trek through lush rainforest to Mti Mkubwa (Big Tree) Camp.', 'image' => 'images/tours/lemosho.jpg', 'image_caption' => 'Mti Mkubwa Camp', 'accommodation' => ['location' => 'Mti Mkubwa Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Mti Mkubwa to Shira 1 Camp', 'description' => 'Climb out of the forest onto the open Shira Plateau, with wide views of Kibo\'s western face.', 'accommodation' => ['location' => 'Shira 1 Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Shira 1 to Shira 2 Camp', 'description' => 'A gentle day crossing the plateau, gaining altitude slowly for acclimatization.', 'accommodation' => ['location' => 'Shira 2 Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Shira 2 to Barranco Camp via Lava Tower', 'description' => 'Climb high to Lava Tower (4,600m) then descend to Barranco — "climb high, sleep low" acclimatization.', 'note' => 'Acclimatization tip: the altitude gain at Lava Tower helps your body adjust before you sleep lower, at Barranco.', 'accommodation' => ['location' => 'Barranco Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Barranco Camp to Karanga Camp', 'description' => 'Scramble up the Barranco Wall, then trek across the Karanga Valley.', 'accommodation' => ['location' => 'Karanga Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Karanga Camp to Barafu Camp', 'description' => 'A shorter day to Barafu, base camp for the summit push. Early rest before the night ascent.', 'note' => 'Early dinner and lights-out — the summit push begins around midnight.', 'accommodation' => ['location' => 'Barafu Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Barafu Camp to Uhuru Peak, descend to Mweka Camp', 'description' => 'Midnight start for the summit push, reaching Uhuru Peak (5,895m) at sunrise, then descend all the way to Mweka Camp.', 'image' => 'images/tours/lemosho.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => ['location' => 'Mweka Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Mweka Camp to Mweka Gate', 'description' => 'Final descent through the rainforest to Mweka Gate, where certificates are issued and your driver meets you.'],
            ],
            'machame-route' => [
                ['title' => 'Machame Gate to Machame Camp', 'description' => 'Register at Machame Gate and trek through montane rainforest to Machame Camp.', 'image' => 'images/tours/machame.jpg', 'image_caption' => 'Machame Camp', 'accommodation' => ['location' => 'Machame Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Machame Camp to Shira Camp', 'description' => 'Climb out of the forest onto moorland, arriving at Shira Camp with views of Kibo.', 'accommodation' => ['location' => 'Shira Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Shira Camp to Barranco Camp via Lava Tower', 'description' => 'Ascend to Lava Tower for acclimatization before descending to Barranco Camp.', 'note' => 'Acclimatization tip: the altitude gain at Lava Tower helps your body adjust before you sleep lower, at Barranco.', 'accommodation' => ['location' => 'Barranco Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Barranco Camp to Karanga Camp', 'description' => 'Tackle the Barranco Wall scramble, then cross to Karanga Camp.', 'accommodation' => ['location' => 'Karanga Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Karanga Camp to Barafu Camp', 'description' => 'A short trek to Barafu, your base camp for the summit attempt.', 'note' => 'Early dinner and lights-out — the summit push begins around midnight.', 'accommodation' => ['location' => 'Barafu Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Barafu Camp to Uhuru Peak, descend to Mweka Camp', 'description' => 'Overnight summit push to Uhuru Peak (5,895m), followed by a long descent to Mweka Camp.', 'image' => 'images/tours/machame.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => ['location' => 'Mweka Camp', 'meal_plan' => 'Full board']],
                ['title' => 'Mweka Camp to Mweka Gate', 'description' => 'Descend through the rainforest to Mweka Gate and transfer back to your hotel.'],
            ],
            'marangu-route' => [
                ['title' => 'Marangu Gate to Mandara Hut', 'description' => 'Register at Marangu Gate and hike through rainforest to Mandara Hut (2,720m).', 'image' => 'images/tours/marangu.jpg', 'image_caption' => 'Mandara Hut', 'accommodation' => ['location' => 'Mandara Hut', 'meal_plan' => 'Full board']],
                ['title' => 'Mandara Hut to Horombo Hut', 'description' => 'Trek through moorland with views of Kibo and Mawenzi peaks to Horombo Hut (3,720m).', 'accommodation' => ['location' => 'Horombo Hut', 'meal_plan' => 'Full board']],
                ['title' => 'Horombo Hut acclimatization day', 'description' => 'An extra day at Horombo to let your body adjust before pushing higher.', 'activity' => ['label' => 'ZEBRA ROCKS ACCLIMATIZATION WALK', 'optional' => true, 'description' => 'A short, easy walk toward the striped Zebra Rocks formation, gaining a little altitude during the day before returning to sleep at Horombo Hut.'], 'accommodation' => ['location' => 'Horombo Hut', 'meal_plan' => 'Full board']],
                ['title' => 'Horombo Hut to Kibo Hut', 'description' => 'Cross the alpine desert saddle between Mawenzi and Kibo to reach Kibo Hut (4,720m), then rest before the summit push.', 'note' => 'Early dinner and lights-out — the summit push begins around midnight.', 'accommodation' => ['location' => 'Kibo Hut', 'meal_plan' => 'Full board']],
                ['title' => 'Kibo Hut to Uhuru Peak, descend to Horombo Hut', 'description' => 'Midnight start for the summit, reaching Uhuru Peak (5,895m) at sunrise, then descend all the way to Horombo Hut.', 'image' => 'images/tours/marangu.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => ['location' => 'Horombo Hut', 'meal_plan' => 'Full board']],
                ['title' => 'Horombo Hut to Marangu Gate', 'description' => 'Final descent through moorland and forest to Marangu Gate for certificates and transfer.'],
            ],
            'northern-circuit-explorer' => [
                ['title' => 'Arrival and transfer to Tarangire', 'description' => 'Land at Kilimanjaro International Airport and drive to Tarangire National Park for an afternoon game drive.', 'image' => 'images/tours/northern-circuit.jpg', 'image_caption' => 'Tarangire National Park', 'accommodation' => ['location' => 'Tarangire', 'meal_plan' => 'Full board']],
                ['title' => 'Tarangire to Serengeti', 'description' => 'Cross the Rift Valley and Ngorongoro Highlands en route to the Serengeti, with game viewing along the way.', 'accommodation' => ['location' => 'Serengeti', 'meal_plan' => 'Full board']],
                ['title' => 'Full day Serengeti game drives', 'description' => 'A full day tracking wildlife across the Serengeti plains, with routing adjusted to that week\'s migration movement.', 'image' => 'images/tours/northern-circuit.jpg', 'image_caption' => 'Serengeti National Park', 'activity' => ['label' => 'HOT AIR BALLOON SAFARI', 'optional' => true, 'description' => 'A sunrise balloon flight over the Serengeti plains, followed by a champagne bush breakfast on landing. Booked locally and paid separately — ask your guide to arrange it.'], 'accommodation' => ['location' => 'Serengeti', 'meal_plan' => 'Full board']],
                ['title' => 'Serengeti to Ngorongoro Crater rim', 'description' => 'Morning game drive before driving to a lodge on the crater rim, arriving in time for sunset views.', 'accommodation' => ['location' => 'Ngorongoro Crater rim', 'meal_plan' => 'Full board']],
                ['title' => 'Full day Ngorongoro Crater floor', 'description' => 'Descend 600m to the crater floor for a full day among its exceptionally dense wildlife population.', 'accommodation' => ['location' => 'Ngorongoro Crater rim', 'meal_plan' => 'Full board']],
                ['title' => 'Departure', 'description' => 'Final morning at leisure before transfer to Kilimanjaro International Airport or on to Zanzibar.'],
            ],
            'southern-circuit-wilderness' => [
                ['title' => 'Arrival and transfer to Ruaha', 'description' => 'Fly or drive into Ruaha National Park and settle into camp ahead of an afternoon game drive.', 'image' => 'images/tours/southern-circuit.jpg', 'image_caption' => 'Ruaha National Park', 'accommodation' => ['location' => 'Ruaha', 'meal_plan' => 'Full board']],
                ['title' => 'Full day Ruaha game drives', 'description' => 'Explore Ruaha\'s baobab-studded landscape, known for exceptional lion and wild dog sightings.', 'accommodation' => ['location' => 'Ruaha', 'meal_plan' => 'Full board']],
                ['title' => 'Ruaha game drive and transfer toward Nyerere', 'description' => 'Morning game drive, then transfer toward Nyerere National Park.', 'accommodation' => ['location' => 'Nyerere', 'meal_plan' => 'Full board']],
                ['title' => 'Nyerere game drives', 'description' => 'Game drives through Nyerere\'s woodlands and open plains.', 'accommodation' => ['location' => 'Nyerere', 'meal_plan' => 'Full board']],
                ['title' => 'Rufiji River boat safari', 'description' => 'A boat safari along the Rufiji River, tracking hippos, crocodiles and riverside wildlife.', 'image' => 'images/tours/southern-circuit.jpg', 'image_caption' => 'Rufiji River', 'activity' => ['label' => 'RUFIJI RIVER BOAT SAFARI', 'optional' => false, 'description' => 'A guided boat safari along the Rufiji River, Nyerere\'s wildlife corridor, tracking hippo pods, crocodiles and riverside birdlife from the water.'], 'accommodation' => ['location' => 'Nyerere', 'meal_plan' => 'Full board']],
                ['title' => 'Final game drive in Nyerere', 'description' => 'One last game drive before settling in for the evening.', 'accommodation' => ['location' => 'Nyerere', 'meal_plan' => 'Full board']],
                ['title' => 'Departure', 'description' => 'Transfer back to the airstrip for your onward flight.'],
            ],
            'zanzibar-beach-escape' => [
                [
                    'title' => 'Arrival, Stone Town',
                    'description' => 'You land at Zanzibar Airport (ZNZ) and are met by a Suni Expedition representative for the transfer to your hotel in Stone Town. The rest of the day is free to explore the old town on foot — the Sultan\'s Palace, the old fort and the winding alleys of the UNESCO World Heritage centre.',
                    'image' => 'images/tours/stone-town.jpg',
                    'image_caption' => 'Stone Town',
                    'note' => 'Check-in starts at 3:00pm. The hotel rate includes breakfast only.',
                    'accommodation' => [
                        'location' => 'Stone Town',
                        'meal_plan' => 'Bed and breakfast',
                        'tiers' => [
                            ['label' => 'Comfort', 'name' => '3-star hotel, Stone Town'],
                            ['label' => 'Premium', 'name' => 'Boutique heritage hotel, Stone Town'],
                        ],
                        'image' => 'images/tours/stone-town.jpg',
                        'image_caption' => 'Stone Town',
                    ],
                ],
                [
                    'title' => 'Stone Town & Spice Tour',
                    'description' => 'After breakfast, transfer out to one of the working spice farms just outside Stone Town before heading north to your beach hotel in Nungwi by early evening.',
                    'image' => 'images/tours/stone-town.jpg',
                    'image_caption' => 'Stone Town',
                    'activity' => [
                        'label' => 'SPICE FARM TOUR',
                        'optional' => false,
                        'image' => 'images/tours/zanzibar-spice-farm.jpg',
                        'description' => 'Walk through a working spice farm and taste cloves, cinnamon, vanilla and chili straight from the plant — the trade that gave Zanzibar its name. Includes a tasting session and a short introduction from your guide.',
                    ],
                    'accommodation' => [
                        'location' => 'Nungwi',
                        'meal_plan' => 'Bed and breakfast',
                        'tiers' => [
                            ['label' => 'Comfort', 'name' => 'Beachfront guesthouse, Nungwi'],
                            ['label' => 'Premium', 'name' => '4-star beach resort, Nungwi'],
                        ],
                        'image' => 'images/tours/zanzibar-beach.jpg',
                        'image_caption' => 'Nungwi Beachfront',
                    ],
                ],
                [
                    'title' => 'Nungwi Beach',
                    'description' => 'A free day on the beach — swim, relax, or join the optional excursion below.',
                    'image' => 'images/tours/zanzibar-beach.jpg',
                    'image_caption' => 'Nungwi Beachfront',
                    'activity' => [
                        'label' => 'DHOW SAILING & SNORKELING',
                        'optional' => true,
                        'image' => 'images/tours/zanzibar-dhow-sunset.jpg',
                        'description' => 'A traditional dhow sailing trip out to Mnemba Atoll and the sandbanks off Nungwi, with snorkeling over the reef and a seafood lunch on board. Booked locally and paid separately — ask your guide to arrange it.',
                    ],
                    'accommodation' => [
                        'location' => 'Nungwi',
                        'meal_plan' => 'Bed and breakfast',
                        'tiers' => [
                            ['label' => 'Comfort', 'name' => 'Beachfront guesthouse, Nungwi'],
                            ['label' => 'Premium', 'name' => '4-star beach resort, Nungwi'],
                        ],
                        'image' => 'images/tours/zanzibar-beach.jpg',
                        'image_caption' => 'Nungwi Beachfront',
                    ],
                ],
                [
                    'title' => 'Beach & Departure',
                    'description' => 'A final morning at leisure on the beach before your transfer back to the airport for your onward flight.',
                    'image' => 'images/tours/zanzibar-beach.jpg',
                    'image_caption' => 'Nungwi Beachfront',
                    'accommodation' => [
                        'meal_plan' => 'Breakfast only',
                    ],
                ],
            ],
            'stone-town-spice-farm-cultural-tour' => [
                [
                    'title' => 'Stone Town & Spice Farm Day Tour',
                    'description' => 'A guided walk through Stone Town\'s coral-stone alleys — the Sultan\'s Palace, the old fort and the House of Wonders — followed by a drive inland to a working spice farm, finished with a included local lunch.',
                    'image' => 'images/tours/stone-town.jpg',
                    'image_caption' => 'Stone Town',
                    'note' => 'Comfortable shoes recommended — Stone Town\'s alleys are mostly shaded, uneven cobblestone.',
                    'activity' => [
                        'label' => 'SPICE FARM TASTING TOUR',
                        'optional' => false,
                        'image' => 'images/tours/zanzibar-spice-farm.jpg',
                        'description' => 'Walk through a working spice farm and taste cloves, cinnamon, vanilla, nutmeg and chili straight from the plant, with a short tasting session and guide introduction.',
                    ],
                    'accommodation' => [
                        'meal_plan' => 'Lunch included',
                    ],
                ],
            ],
        ];

        foreach ($itineraries as $slug => $days) {
            $tour = Tour::where('slug', $slug)->first();

            if (! $tour) {
                continue;
            }

            foreach ($days as $index => $day) {
                $tour->itineraryItems()->updateOrCreate(
                    ['day_number' => $index + 1],
                    [
                        'title' => $day['title'],
                        'description' => $day['description'],
                        'image' => $day['image'] ?? null,
                        'image_caption' => $day['image_caption'] ?? null,
                        'note' => $day['note'] ?? null,
                        'activity' => $day['activity'] ?? null,
                        'accommodation' => $day['accommodation'] ?? null,
                    ]
                );
            }
        }
    }
}
