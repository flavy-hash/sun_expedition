<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

/**
 * Zanzibar holidays: beach stays, safari + Zanzibar combinations and the Mafia / Pemba island extensions.
 * Only adds packages whose slug doesn't exist yet, so it never overwrites anything edited in the admin.
 * Safe on production:
 *   php artisan db:seed --class=ZanzibarHolidaysSeeder --force
 *
 * Each night lists three hotel options (Comfort / Premium / Luxury) matching the package level the guest picks.
 * Prices are per-person starting points for a group of six in low season; review them in the admin
 * (Packages → Zanzibar).
 */
class ZanzibarHolidaysSeeder extends Seeder
{
    private const BEACH_HOTELS = ['Fun Beach Resort', 'Legendary Zanzibar Beach Resort', 'Qambani Luxury Resort'];

    private const ARUSHA_HOTELS = ['Ahadi Lodge', 'Giraffe Manor Tanzania', 'Giraffe Manor Tanzania'];

    private const TARANGIRE_HOTELS = ['Manyara Best View Lodge and Spa', "Ang'ata Tarangire Camp", 'Lake Manyara Kilimamoja Lodge by Wellworth'];

    private const SERENGETI_HOTELS = ['Serengeti Sound of Silence Tented Camp', 'Signature Serengeti Luxury Tented Camp', 'Serengeti Sametu Camp by Karibu Camps and Lodges'];

    private const NGORONGORO_HOTELS = ['Manyara Best View Lodge and Spa', 'Manyara Best View Lodge and Spa', "Ngorongoro O'ldeani Mountain Lodge by Wellworth"];

    private const COMBO_INCLUDED = [
        'Private 4x4 safari vehicle with your own English-speaking guide',
        'Full board on safari days',
        'All park and Ngorongoro Crater fees',
        'Game drives as described',
        'Flight from the safari to Zanzibar',
        'Airport pickups and all transfers',
        'All accommodation, with breakfast daily on Zanzibar',
        '24/7 support during your trip',
    ];

    private const BEACH_INCLUDED = ['All accommodation', 'Daily breakfast', 'Airport transfers on arrival and departure', '24/7 support during your stay'];

    private const EXCLUDED = [
        'International flights (we can book them on request)',
        'Tanzania visa',
        'Travel insurance',
        'Optional excursions on beach days',
        'Lunch and dinner on Zanzibar unless your hotel plan includes them',
        'Tips and personal expenses',
    ];

    private const BEST_TIME = 'June – October, December – February';

    public function run(): void
    {
        $created = 0;

        foreach ($this->packages() as $order => $package) {
            if (Tour::where('slug', $package['slug'])->exists()) {
                continue;
            }

            $days = $package['days'];
            unset($package['days']);

            $tour = Tour::create($package + [
                'category' => 'zanzibar',
                'group_size_max' => 6,
                'best_time' => self::BEST_TIME,
                'excluded' => self::EXCLUDED,
                'is_featured' => false,
                'sort_order' => 3 + $order,
            ]);

            foreach ($days as $index => $day) {
                $tour->itineraryItems()->create($day + ['day_number' => $index + 1]);
            }

            $created++;
        }

        $this->command?->info($created ? "Added {$created} Zanzibar packages." : 'All Zanzibar packages already exist, nothing added.');
    }

    private function packages(): array
    {
        return [
            [
                'title' => '10 Days Zanzibar Island',
                'slug' => '10-days-zanzibar-island',
                'summary' => 'Nine nights on the Indian Ocean with Stone Town, Safari Blue and the reef at your own pace.',
                'description' => "Ten days is long enough to properly slow down on Zanzibar. You stay at one beach resort the whole time, so there's no packing and unpacking, and fill the days as you like: a dhow trip to the sandbanks of Menai Bay, a walk through the alleys of Stone Town, the red colobus monkeys of Jozani Forest or simply the beach. Every excursion is optional and can be booked before you travel or once you've arrived. It works well on its own or as an add-on to a Mafia or Pemba stay.",
                'duration_days' => 10,
                'price_from' => 625,
                'difficulty' => 'Relaxed',
                'highlight_stat' => 'One resort, nine nights',
                'location' => 'Zanzibar',
                'hero_image' => 'images/tours/nungwi-beach.jpg',
                'highlights' => ['White sand and warm Indian Ocean water', 'Stone Town, a UNESCO World Heritage Site', 'Safari Blue dhow trip to the Menai Bay sandbanks', 'Snorkelling at Mnemba Atoll', 'Swahili food that mixes African, Arab and Indian cooking'],
                'included' => self::BEACH_INCLUDED,
                'days' => [
                    $this->zanzibarArrival(),
                    ...$this->beachDays(['safari-blue', 'stone-town', null, 'jozani', 'prison-island', 'mnemba', null, 'spice-farm']),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '6 Days Zanzibar Beach Extension',
                'slug' => '6-days-zanzibar-beach-extension',
                'summary' => 'Five nights on the beach to wind down after a safari or Kilimanjaro climb.',
                'description' => "The easy way to end a safari or a climb: fly to Zanzibar and swap the dust for five nights by the Indian Ocean. We meet you at the airport and take you straight to your beach resort. After that the days are yours, with a Safari Blue dhow trip and a Stone Town tour as our two favourite add-ons if you want to do more than swim and sleep.",
                'duration_days' => 6,
                'price_from' => 348,
                'difficulty' => 'Relaxed',
                'highlight_stat' => 'Safari add-on',
                'location' => 'Zanzibar',
                'hero_image' => 'images/tours/zanzibar-white-sand.jpg',
                'highlights' => ['Five nights at a beach resort', 'Safari Blue dhow cruise (optional)', 'Guided Stone Town walk (optional)', 'Easy to add to any safari or climb'],
                'included' => self::BEACH_INCLUDED,
                'days' => [
                    $this->zanzibarArrival(),
                    ...$this->beachDays(['safari-blue', 'stone-town', null, 'jozani']),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '11 Days Safari & Zanzibar',
                'slug' => '11-days-safari-and-zanzibar',
                'summary' => 'Tarangire, the Serengeti and Ngorongoro Crater on a private safari, then five nights on Zanzibar.',
                'description' => "The classic Tanzania combination in eleven days. A private safari with your own guide and 4x4 takes you to Tarangire's elephant herds, two full days in the central Serengeti and down into the Ngorongoro Crater, where black rhino are seen more reliably than anywhere else in the north. Then you fly to Zanzibar for beach time, with Stone Town, the spice farms and a Safari Blue dhow trip there if you want them.",
                'duration_days' => 11,
                'price_from' => 2181,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => 'Big Five + beach',
                'location' => 'Northern Tanzania & Zanzibar',
                'hero_image' => 'images/tours/paje-beach.jpg',
                'highlights' => ['Private 4x4 and guide throughout the safari', 'Two days in the central Serengeti', 'Black rhino in the Ngorongoro Crater', 'Elephants and baobabs in Tarangire', 'Flight to Zanzibar included'],
                'included' => self::COMBO_INCLUDED,
                'days' => [
                    $this->kilimanjaroArrival(),
                    $this->tarangire(['Manyara Best View Lodge and Spa', "Manyara's Secret", 'Lake Manyara Kilimamoja Lodge by Wellworth']),
                    ...$this->serengeti(2, ['Serengeti Sound of Silence Tented Camp', 'Luwela Camp', 'Ole Serai Luxury Camp - Turner Springs by Wellworth']),
                    $this->ngorongoro(),
                    $this->flightToZanzibar(),
                    ...$this->beachDays(['safari-blue', 'stone-town', 'spice-farm', null]),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '12 Days Honeymoon Tanzania & Zanzibar',
                'slug' => '12-days-honeymoon-tanzania-and-zanzibar',
                'summary' => 'A private safari for two with a slow day at the lodge, then five nights of Zanzibar sunsets.',
                'description' => "Built for newlyweds who want romance with a bit of adventure. Your private safari covers Tarangire, the Serengeti, with the option of a sunrise balloon flight, and the Ngorongoro Crater, and includes a slow day at the lodge to rest, swim or take a guided walk. Then you fly to Zanzibar for five nights by the ocean, with a sunset dhow cruise and Stone Town on hand whenever you want them.",
                'duration_days' => 12,
                'price_from' => 2500,
                'difficulty' => 'Honeymoon',
                'highlight_stat' => 'Private safari for two',
                'location' => 'Northern Tanzania & Zanzibar',
                'hero_image' => 'images/tours/zanzibar-honeymoon.jpg',
                'highlights' => ['Your own vehicle and guide, at your own pace', 'Optional hot-air balloon over the Serengeti', 'A slow day at the lodge mid-safari', 'Five nights on the beach', 'Flight to Zanzibar included'],
                'included' => self::COMBO_INCLUDED,
                'days' => [
                    $this->kilimanjaroArrival(['Villa Maua', 'Giraffe Manor Tanzania', 'Giraffe Manor Tanzania']),
                    $this->tarangire(),
                    ...$this->serengeti(2, ['Serengeti Sound of Silence Tented Camp', 'Signature Serengeti Luxury Tented Camp', 'Nimali Central Serengeti'], balloon: true),
                    $this->ngorongoro(),
                    [
                        'title' => 'A slow day near Lake Manyara',
                        'description' => "No early start today. Spend it at the lodge pool, take a guided nature walk or a bike ride through the farms and villages around Mto wa Mbu, or join an evening game drive outside the park. It's a deliberately quiet day before the beach.",
                        'image' => 'images/stays/tarangire-riverside-lodge.jpg',
                        'image_caption' => 'Lodge day',
                        'map_place' => 'mto-wa-mbu',
                        'accommodation' => $this->stay('Lake Manyara', 'Full board', self::NGORONGORO_HOTELS, 'images/stays/tarangire-riverside-lodge.jpg', 'Lake Manyara'),
                    ],
                    $this->flightToZanzibar(),
                    ...$this->beachDays(['sunset-dhow', 'stone-town', null, 'mnemba']),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '12 Days Safari & Beach — Mara River Crossings',
                'slug' => '12-days-safari-and-beach-mara-river',
                'summary' => 'The Northern Serengeti in migration season, with a chance of river crossings, then a flight to the beach.',
                'description' => "A safari timed for the Great Migration's most dramatic stretch. After Tarangire and the Ngorongoro Crater you work your way north through the central Serengeti to the Mara River, where the herds cross from late July to early October. Crossings are never guaranteed, but two full days in the north give you a real chance. You then fly to Zanzibar for four nights on the beach.",
                'duration_days' => 12,
                'price_from' => 3154,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => 'Mara River crossings',
                'location' => 'Serengeti & Zanzibar',
                'hero_image' => 'images/blog/great-migration.jpg',
                'best_time' => 'Late July – early October for river crossings',
                'highlights' => ['Two days in the Northern Serengeti', 'A chance of Mara River crossings (July – October)', 'Ngorongoro Crater game drive', 'Elephants and baobabs in Tarangire', 'Flight to Zanzibar included'],
                'included' => self::COMBO_INCLUDED,
                'days' => [
                    $this->kilimanjaroArrival(),
                    $this->tarangire(['Manyara Best View Lodge and Spa', 'Mawe Mawe Manyara Lodge', 'Manyara Best View Lodge and Spa (upgraded room)']),
                    $this->ngorongoro(['Ngorongoro Kuhama Camp', "Ngorongoro Lion's Paw Camp", "Ngorongoro Lion's Paw Camp"], 'Ngorongoro Highlands', 'images/stays/ngorongoro-highlands-farmhouse.jpg'),
                    ...$this->serengeti(1, ['Serengeti Sound of Silence Tented Camp', 'Asanja Nyasirori', 'Serengeti Sametu Camp']),
                    [
                        'title' => 'Northern Serengeti & the Mara River',
                        'description' => "Drive north to the Kogatende area, where the migration gathers along the Mara River in the dry season. Your guide watches the herds massing on the banks and positions you for a crossing, which can come in minutes or not at all. The landscape up here is rolling and green, and much quieter than the central plains.",
                        'image' => 'images/blog/great-migration.jpg',
                        'image_caption' => 'Mara River',
                        'map_place' => 'northern-serengeti',
                        'note' => 'River crossings happen roughly from late July to early October and are never guaranteed.',
                        'accommodation' => $this->stay('Northern Serengeti', 'Full board', ['Baobab Mara Luxury Camp', 'Serengeti Mara River Camp', 'Nimali Mara'], 'images/stays/serengeti-tented-camp.jpg', 'Northern Serengeti'),
                    ],
                    [
                        'title' => 'Northern Serengeti full day',
                        'description' => "A second full day by the river with packed lunches, so you can wait out a crossing rather than head back to camp. Away from the river there are lion prides, cheetah and big herds of topi and eland on the Lamai plains.",
                        'image' => 'images/tours/northern-circuit.jpg',
                        'image_caption' => 'Northern Serengeti',
                        'map_place' => 'northern-serengeti',
                        'accommodation' => $this->stay('Northern Serengeti', 'Full board', ['Baobab Mara Luxury Camp', 'Serengeti Mara River Camp', 'Nimali Mara'], 'images/stays/serengeti-tented-camp.jpg', 'Northern Serengeti'),
                    ],
                    $this->flightToZanzibar(['Fun Beach Resort', 'Next Paradise Boutique Resort', 'Qambani Luxury Resort'], 'Your guide drives you to the Kogatende airstrip for a light-aircraft flight to Zanzibar, connecting through Arusha.'),
                    ...$this->beachDays(['safari-blue', 'stone-town', null, null], ['Fun Beach Resort', 'Next Paradise Boutique Resort', 'Qambani Luxury Resort']),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '13 Days Safari & Zanzibar Holiday',
                'slug' => '13-days-safari-and-zanzibar',
                'summary' => 'An unhurried start in Arusha, a private safari through the north and four nights on Zanzibar.',
                'description' => "This trip starts slowly on purpose. Three nights near Arusha give you time to get over the flight, take a first game drive in Arusha National Park and hike to the waterfalls on Mount Meru's slopes. From there your private safari covers Tarangire, two days in the Serengeti and the Ngorongoro Crater before you fly to Zanzibar for the beach, Stone Town and the spice farms.",
                'duration_days' => 13,
                'price_from' => 2568,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => 'Relaxed pace',
                'location' => 'Northern Tanzania & Zanzibar',
                'hero_image' => 'images/tours/zanzibar-dhow-sunset.jpg',
                'highlights' => ['Colobus monkeys in Arusha National Park', 'Waterfall hike on Mount Meru', 'Two days in the Serengeti', 'Ngorongoro Crater', 'Flight to Zanzibar included'],
                'included' => self::COMBO_INCLUDED,
                'days' => [
                    $this->kilimanjaroArrival(),
                    ...$this->arushaDays(),
                    $this->tarangire(),
                    ...$this->serengeti(2),
                    $this->ngorongoro(),
                    $this->flightToZanzibar(),
                    ...$this->beachDays(['safari-blue', 'stone-town', null]),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '15 Days Tanzania Safari & Zanzibar',
                'slug' => '15-days-tanzania-safari-and-zanzibar',
                'summary' => 'Arusha, Tarangire, three days in the Serengeti and the Crater, then five nights on Zanzibar.',
                'description' => "Fifteen days lets you see the north properly without rushing. After three easy days around Arusha you spend a day in Tarangire, three full days following the herds through the Serengeti and a morning in the Ngorongoro Crater. Then you fly to Zanzibar for five nights of beach, reef and Stone Town.",
                'duration_days' => 15,
                'price_from' => 3039,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => '3 days in the Serengeti',
                'location' => 'Northern Tanzania & Zanzibar',
                'hero_image' => 'images/tours/zanzibar-shoreline.jpg',
                'highlights' => ['Three full days in the Serengeti', 'Arusha National Park and the Mount Meru waterfalls', 'Ngorongoro Crater', 'Five nights on Zanzibar', 'Flight to Zanzibar included'],
                'included' => self::COMBO_INCLUDED,
                'days' => [
                    $this->kilimanjaroArrival(),
                    ...$this->arushaDays(),
                    $this->tarangire(),
                    ...$this->serengeti(3),
                    $this->ngorongoro(),
                    $this->flightToZanzibar(),
                    ...$this->beachDays(['safari-blue', 'stone-town', 'jozani', null]),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '18 Days Tanzania Safari & Zanzibar Vacation',
                'slug' => '18-days-tanzania-safari-and-zanzibar',
                'summary' => 'The long version: an unhurried northern safari followed by a full week on Zanzibar.',
                'description' => "For travellers who want time on both sides of the trip. You start with three easy days near Arusha, then follow the same private safari route as our 15-day trip through Tarangire, three days in the Serengeti, which can be timed for the wildebeest migration, and the Ngorongoro Crater. After that comes a full week on Zanzibar, long enough for the reef at Mnemba, the forest at Jozani, Stone Town and plenty of days doing nothing at all.",
                'duration_days' => 18,
                'price_from' => 3204,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => 'A week on the beach',
                'location' => 'Northern Tanzania & Zanzibar',
                'hero_image' => 'images/tours/dhow-sunset-boats.jpg',
                'highlights' => ['Three full days in the Serengeti', 'Can be timed for the wildebeest migration', 'Ngorongoro Crater', 'Seven nights on Zanzibar', 'Snorkelling at Mnemba and diving on request'],
                'included' => self::COMBO_INCLUDED,
                'days' => [
                    $this->kilimanjaroArrival(),
                    ...$this->arushaDays(),
                    $this->tarangire(),
                    ...$this->serengeti(3),
                    $this->ngorongoro(),
                    $this->flightToZanzibar(),
                    ...$this->beachDays(['safari-blue', 'stone-town', null, 'jozani', 'mnemba', 'prison-island', null]),
                    $this->zanzibarDeparture(),
                ],
            ],
            [
                'title' => '6 Days Mafia Island',
                'slug' => '6-days-mafia-island',
                'summary' => 'Whale sharks, a protected marine park and quiet beaches on Tanzania\'s least-visited island.',
                'description' => "Mafia is what Zanzibar was before the resorts: a handful of small lodges, fishing villages and one of the best marine parks in the Indian Ocean. From October to March whale sharks feed off Kilindoni, and you can snorkel alongside them. The rest of the year, the coral gardens of Chole Bay, the ruins on Chole Island and long empty beaches make it a quiet place to finish a safari.",
                'duration_days' => 6,
                'price_from' => 667,
                'difficulty' => 'Island escape',
                'highlight_stat' => 'Whale sharks (Oct – Mar)',
                'location' => 'Mafia Island',
                'hero_image' => 'images/tours/mafia-whale-shark.jpg',
                'best_time' => 'October – March for whale sharks; June – October for diving',
                'highlights' => ['Snorkel with whale sharks (October – March)', 'Coral reefs of Mafia Island Marine Park', '19th-century ruins on Chole Island', 'Small beachfront lodges and fresh seafood'],
                'included' => self::BEACH_INCLUDED,
                'excluded' => ['Flights to and from Mafia (we book them for you)', ...self::EXCLUDED],
                'days' => [
                    $this->islandDay('Arrival in Mafia', "A short flight from Dar es Salaam or Zanzibar lands you at Kilindoni, where your lodge driver meets you for the drive across the island to Chole Bay. The rest of the day is for settling in and the first swim.", 'images/tours/mafia-island.jpg', 'Chole Bay', 'mafia-airport', 'mafia'),
                    $this->islandDay('Whale sharks off Kilindoni', "Head out by boat from Kilindoni to look for whale sharks feeding in the shallow bay. Spotters on board guide you in and you slip into the water to snorkel alongside them. It's calm and unhurried, and the boats keep their numbers low.", 'images/tours/mafia-whale-shark.jpg', 'Whale sharks', 'mafia-airport', 'mafia', $this->activity('WHALE SHARK SNORKELLING', 'images/tours/mafia-whale-shark.jpg', 'A half-day boat trip with a spotter and snorkelling guide. Whale sharks are in the bay from roughly October to March; outside that season we swap this for a reef trip.'), 'Whale sharks are seasonal (October – March).'),
                    $this->islandDay('Mafia Island Marine Park', "A boat trip into the marine park for snorkelling or diving over the coral gardens of Chole Bay, with turtles, reef fish and, if you're lucky, dolphins on the way.", 'images/tours/mnemba-snorkelling.jpg', 'Marine park', 'mafia-marine-park', 'mafia', $this->activity('SNORKELLING OR DIVING', 'images/tours/mnemba-snorkelling.jpg', 'Guided snorkelling or two dives inside the marine park. Diving is arranged with the lodge dive centre and suits both beginners and certified divers.')),
                    $this->islandDay('Chole Island ruins', "Cross the bay by dhow to Chole Island, where fig trees grow through the walls of 19th-century merchant houses. Fruit bats roost in the trees and the village is one of the oldest settlements on the coast.", 'images/tours/mafia-island.jpg', 'Chole Island', 'chole-island', 'mafia', $this->activity('CHOLE ISLAND WALK', 'images/tours/mafia-island.jpg', 'A guided walk through the ruins and the village, about two hours by dhow and on foot.')),
                    $this->islandDay('Beach day', 'A free day on the beach or around the lodge, with kayaks and sunset dhow sails usually on offer.', 'images/tours/mafia-island.jpg', 'Mafia Island', 'mafia-marine-park', 'mafia'),
                    ['title' => 'Departure from Mafia', 'description' => 'After breakfast your driver takes you back to Kilindoni for your flight to Dar es Salaam or Zanzibar and your connection home.', 'image' => 'images/tours/mafia-island.jpg', 'image_caption' => 'Mafia Island', 'map_place' => 'mafia-airport'],
                ],
            ],
            [
                'title' => '6 Days Pemba Island',
                'slug' => '6-days-pemba-island',
                'summary' => 'Green hills, mangroves and some of East Africa\'s best diving on Zanzibar\'s quiet sister island.',
                'description' => "Pemba sits north of Zanzibar and sees a fraction of its visitors. It's greener and hillier, ringed with mangroves and steep coral walls that make it one of the best diving destinations in East Africa. Stay at a beach lodge, snorkel at Misali Island, walk in Ngezi Forest and spend the rest of the time on near-empty beaches like Vumawimbi.",
                'duration_days' => 6,
                'price_from' => 994,
                'difficulty' => 'Island escape',
                'highlight_stat' => 'World-class diving',
                'location' => 'Pemba Island',
                'hero_image' => 'images/tours/pemba-island.jpg',
                'highlights' => ['Snorkelling and diving at Misali Island', 'Ngezi Forest and Vumawimbi Beach', 'Mangrove channels by kayak', 'Swahili villages and seafood'],
                'included' => self::BEACH_INCLUDED,
                'excluded' => ['Flights to and from Pemba (we book them for you)', ...self::EXCLUDED],
                'days' => [
                    $this->islandDay('Arrival in Pemba', 'A short flight from Zanzibar or Dar es Salaam lands you at Chake Chake, where your driver meets you for the transfer to your beach lodge.', 'images/tours/pemba-island.jpg', 'Pemba Island', 'pemba-airport', 'pemba'),
                    $this->islandDay('Misali Island', 'A boat trip to Misali Island, a protected reef with clear water, turtles and nature trails through the island forest.', 'images/tours/mnemba-snorkelling.jpg', 'Misali Island', 'misali-island', 'pemba', $this->activity('MISALI SNORKELLING', 'images/tours/mnemba-snorkelling.jpg', 'A full-day trip with snorkelling on the reef, a forest walk and a beach lunch. Divers can swap snorkelling for two dives on the coral walls.')),
                    $this->islandDay('Ngezi Forest & Vumawimbi Beach', "Walk through Ngezi Forest, home to the Pemba flying fox found nowhere else, then continue to Vumawimbi, one of the island's longest and emptiest beaches.", 'images/tours/pemba-island.jpg', 'Vumawimbi Beach', 'ngezi-forest', 'pemba', $this->activity('NGEZI FOREST WALK', 'images/tours/pemba-island.jpg', 'A guided walk of about two hours with a forest ranger, followed by an afternoon at Vumawimbi Beach.')),
                    $this->islandDay('Mangroves by kayak', 'Paddle the mangrove channels near the lodge at high tide, then spend the afternoon on the beach.', 'images/tours/pemba-island.jpg', 'Mangroves', 'pemba-airport', 'pemba'),
                    $this->islandDay('Beach day', 'A free day to dive, snorkel off the house reef or do nothing at all.', 'images/tours/pemba-island.jpg', 'Pemba Island', 'pemba-airport', 'pemba'),
                    ['title' => 'Departure from Pemba', 'description' => 'After breakfast your driver takes you to Chake Chake airport for your flight to Zanzibar or Dar es Salaam and your connection home.', 'image' => 'images/tours/pemba-island.jpg', 'image_caption' => 'Pemba Island', 'map_place' => 'pemba-airport'],
                ],
            ],
        ];
    }

    // ---- Safari days -----------------------------------------------------------------------------------------

    private function kilimanjaroArrival(array $hotels = self::ARUSHA_HOTELS): array
    {
        return [
            'title' => 'Arrival at Kilimanjaro Airport',
            'description' => 'Your guide meets you at Kilimanjaro International Airport and drives you to your lodge on the outskirts of Arusha, about an hour away. The rest of the day is free to rest after the flight.',
            'image' => 'images/stays/arusha-garden-lodge.jpg',
            'image_caption' => 'Arusha',
            'map_place' => 'kilimanjaro-airport',
            'accommodation' => $this->stay('Arusha', 'Bed and breakfast', $hotels, 'images/stays/arusha-garden-lodge.jpg', 'Arusha'),
        ];
    }

    private function arushaDays(): array
    {
        $stay = $this->stay('Arusha', 'Bed and breakfast', self::ARUSHA_HOTELS, 'images/stays/arusha-garden-lodge.jpg', 'Arusha');

        return [
            [
                'title' => 'A relaxed day in Arusha',
                'description' => 'A free day to get over the journey. Stay at the lodge, or let us arrange a city tour, a mountain-bike ride or a visit to the Maasai market.',
                'image' => 'images/stays/arusha-garden-lodge.jpg',
                'image_caption' => 'Arusha',
                'map_place' => 'arusha',
                'accommodation' => $stay,
            ],
            [
                'title' => 'Arusha National Park',
                'description' => 'Your first game drive, in a small park on the slopes of Mount Meru. Look for black-and-white colobus monkeys in the forest, giraffe and buffalo in the Ngurdoto Crater and flamingos on the Momella Lakes.',
                'image' => 'images/hero/safari.jpg',
                'image_caption' => 'Arusha National Park',
                'map_place' => 'arusha-national-park',
                'accommodation' => $stay,
            ],
            [
                'title' => 'Relax or hike to the Mount Meru waterfalls',
                'description' => 'Take it easy at the lodge, or walk with a ranger through the forest to the Napuru waterfall on the lower slopes of Mount Meru for a swim in the pool below.',
                'image' => 'images/hero/kilimanjaro.jpg',
                'image_caption' => 'Mount Meru',
                'map_place' => 'arusha-national-park',
                'activity' => $this->activity('WATERFALL HIKE', 'images/hero/kilimanjaro.jpg', 'A guided half-day walk with an armed park ranger to the waterfall, with time to swim.'),
                'accommodation' => $stay,
            ],
        ];
    }

    private function tarangire(array $hotels = self::TARANGIRE_HOTELS): array
    {
        return [
            'title' => 'Tarangire National Park',
            'description' => "Drive west to Tarangire, where herds of up to 250 elephants gather along the river in the dry season between ancient baobab trees. Lion, giraffe and huge flocks of birds are common, and the park is far quieter than the Serengeti.",
            'image' => 'images/tours/tarangire-manyara.jpg',
            'image_caption' => 'Tarangire',
            'map_place' => 'tarangire',
            'accommodation' => $this->stay('Tarangire / Lake Manyara', 'Full board', $hotels, 'images/stays/tarangire-riverside-lodge.jpg', 'Tarangire'),
        ];
    }

    private function serengeti(int $days, array $hotels = self::SERENGETI_HOTELS, bool $balloon = false): array
    {
        $stay = $this->stay('Central Serengeti', 'Full board', $hotels, 'images/stays/serengeti-tented-camp.jpg', 'Serengeti');

        $first = [
            'title' => 'Into the Serengeti',
            'description' => 'Cross the Ngorongoro highlands and descend onto the open plains of the Serengeti, game-driving all the way to your camp in the central Seronera area, where lion, leopard and cheetah are seen year-round.',
            'image' => 'images/tours/northern-circuit.jpg',
            'image_caption' => 'Serengeti',
            'map_place' => 'serengeti',
            'accommodation' => $stay,
        ];

        $full = [
            'title' => 'Serengeti full day',
            'description' => 'A full day of game drives with a picnic lunch. Your guide follows the migration herds when they are nearby and the big cats of the Seronera valley when they are not.',
            'image' => 'images/nav/safari-lion.jpg',
            'image_caption' => 'Serengeti',
            'map_place' => 'serengeti',
            'accommodation' => $stay,
        ];

        if ($balloon) {
            $full['activity'] = $this->activity('HOT-AIR BALLOON SAFARI', 'images/blog/great-migration.jpg', 'Lift off at dawn for an hour over the plains, followed by a champagne breakfast in the bush. Book ahead; it sells out in high season.');
        }

        return [$first, ...array_fill(0, $days - 1, $full)];
    }

    private function ngorongoro(array $hotels = self::NGORONGORO_HOTELS, string $location = 'Ngorongoro / Lake Manyara', string $image = 'images/stays/ngorongoro-highlands-farmhouse.jpg'): array
    {
        return [
            'title' => 'Ngorongoro Crater',
            'description' => 'Descend 600 metres to the floor of the Ngorongoro Crater for a game drive in the densest concentration of wildlife in Africa. This is the most reliable place in the north to see black rhino, along with lion, hyena, elephant and huge herds of zebra and wildebeest.',
            'image' => 'images/tours/northern-circuit.jpg',
            'image_caption' => 'Ngorongoro Crater',
            'map_place' => 'ngorongoro',
            'accommodation' => $this->stay($location, 'Full board', $hotels, $image, 'Ngorongoro'),
        ];
    }

    // ---- Zanzibar days ---------------------------------------------------------------------------------------

    private function flightToZanzibar(array $hotels = self::BEACH_HOTELS, ?string $description = null): array
    {
        return [
            'title' => 'Flight to Zanzibar',
            'description' => $description ?? 'Your guide drives you to Arusha airport for the short flight to Zanzibar, which is included. A driver meets you on arrival and takes you to your beach resort.',
            'image' => 'images/tours/zanzibar-beach.jpg',
            'image_caption' => 'Zanzibar',
            'map_place' => 'zanzibar-airport',
            'note' => 'Lunch and dinner on Zanzibar depend on your hotel plan. Breakfast is always included.',
            'accommodation' => $this->beachStay($hotels),
        ];
    }

    private function zanzibarArrival(): array
    {
        return [
            'title' => 'Arrival in Zanzibar',
            'description' => 'A driver meets you at Zanzibar Airport (ZNZ) and takes you to your beach resort. If you land early, ask to stop in Stone Town on the way for your first taste of the island and its food.',
            'image' => 'images/tours/zanzibar-beach.jpg',
            'image_caption' => 'Zanzibar',
            'map_place' => 'zanzibar-airport',
            'accommodation' => $this->beachStay(),
        ];
    }

    private function zanzibarDeparture(): array
    {
        return [
            'title' => 'Departure from Zanzibar',
            'description' => 'A last morning on the beach before your driver takes you to Zanzibar Airport (ZNZ) for your flight home. You can also extend your trip with a few nights on Mnemba, Pemba or Mafia.',
            'image' => 'images/tours/zanzibar-dhow-sunset.jpg',
            'image_caption' => 'Zanzibar',
            'map_place' => 'zanzibar-airport',
        ];
    }

    /** One beach day per entry; each entry is an optional excursion key or null for a free day. */
    private function beachDays(array $plan, array $hotels = self::BEACH_HOTELS): array
    {
        $excursions = [
            'safari-blue' => ['Safari Blue dhow trip', 'Sail by traditional dhow into Menai Bay for a day of snorkelling, a swim on a sandbank and a seafood barbecue on Kwale Island.', 'images/tours/zanzibar-white-sand.jpg', 'Menai Bay', 'kwale-island', 'SAFARI BLUE', 'A full-day dhow cruise with snorkelling gear, fresh fruit and a seafood lunch. Collection from your hotel is included.'],
            'stone-town' => ['Stone Town walking tour', "Explore the UNESCO-listed old town with a local guide: the House of Wonders, the Old Fort, the former slave market and Freddie Mercury's birthplace, ending at the Forodhani night food market.", 'images/tours/stone-town.jpg', 'Stone Town', 'stone-town', 'STONE TOWN TOUR', 'A three-hour guided walk through the alleys and markets of Stone Town.'],
            'jozani' => ['Jozani Forest', "Visit Zanzibar's last indigenous forest, home to the rare red colobus monkey found only on the island, and walk the boardwalk through the mangroves.", 'images/tours/jozani-colobus.jpg', 'Jozani Forest', 'jozani', 'JOZANI FOREST', 'A half-day guided walk in Jozani Chwaka Bay National Park, about two hours from the north coast.'],
            'prison-island' => ['Prison Island', 'A short boat ride from Stone Town to Changuu, the old quarantine island, now home to giant Aldabra tortoises, a small beach and good snorkelling.', 'images/tours/prison-island-tortoises.jpg', 'Prison Island', 'prison-island', 'PRISON ISLAND', 'A half-day boat trip with snorkelling and a visit to the tortoise sanctuary.'],
            'mnemba' => ['Snorkelling at Mnemba Atoll', 'A boat trip to the reef around Mnemba Atoll, with some of the clearest water off Zanzibar, turtles, reef fish and often dolphins on the way.', 'images/tours/mnemba-snorkelling.jpg', 'Mnemba Atoll', 'mnemba', 'MNEMBA SNORKELLING', 'A half-day boat trip with snorkelling gear and a guide. Diving can be arranged instead.'],
            'spice-farm' => ['Spice farm tour', 'Walk through a working spice farm and taste cloves, cinnamon, vanilla and nutmeg straight from the plant — the trade that made Zanzibar famous.', 'images/tours/zanzibar-spice-farm.jpg', 'Spice farm', 'spice-farm', 'SPICE FARM TOUR', 'A half-day guided tour with a tasting of fresh fruit and spices.'],
            'sunset-dhow' => ['Sunset dhow cruise', 'Laze on the beach all day, then sail out at dusk on a traditional dhow to watch the sun go down over the Indian Ocean.', 'images/tours/zanzibar-honeymoon.jpg', 'Sunset dhow', 'nungwi', 'SUNSET DHOW CRUISE', 'A two-hour evening sail with drinks and snacks on board.'],
        ];

        $stay = $this->beachStay($hotels);

        return collect($plan)->values()->map(function (?string $key, int $index) use ($excursions, $stay): array {
            if ($key === null) {
                return [
                    'title' => 'Beach day',
                    'description' => 'A free day on the beach. Swim, read, walk the shore at low tide or try kitesurfing, paddleboarding or a spa treatment at the resort.',
                    'image' => $index % 2 ? 'images/tours/nungwi-beach.jpg' : 'images/tours/paje-beach.jpg',
                    'image_caption' => 'Zanzibar',
                    'map_place' => 'kiwengwa',
                    'accommodation' => $stay,
                ];
            }

            [$title, $description, $image, $caption, $place, $label, $activityText] = $excursions[$key];

            return [
                'title' => $title,
                'description' => $description,
                'image' => $image,
                'image_caption' => $caption,
                'map_place' => $place,
                'note' => $index === 0 ? 'Excursions on beach days are optional. Book them with your trip or once you arrive.' : null,
                'activity' => $this->activity($label, $image, $activityText),
                'accommodation' => $stay,
            ];
        })->all();
    }

    // ---- Island extensions -----------------------------------------------------------------------------------

    private function islandDay(string $title, string $description, string $image, string $caption, string $place, string $island, ?array $activity = null, ?string $note = null): array
    {
        $hotels = $island === 'mafia'
            ? ['Mafia Kivulini Lodge', 'Basecamp Eco Resort Mafia Island', 'Pole Pole Bungalows']
            : ['Pemba Paradise Lodge', 'Fundu Lagoon', 'The Manta Resort'];

        return array_filter([
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'image_caption' => $caption,
            'map_place' => $place,
            'note' => $note,
            'activity' => $activity,
            'accommodation' => $this->stay($island === 'mafia' ? 'Mafia Island' : 'Pemba Island', 'Bed and breakfast', $hotels, $image, $island === 'mafia' ? 'Mafia Island' : 'Pemba Island'),
        ], fn ($value) => $value !== null);
    }

    // ---- Building blocks -------------------------------------------------------------------------------------

    private function beachStay(array $hotels = self::BEACH_HOTELS): array
    {
        return $this->stay('Zanzibar beach resort', 'Bed and breakfast', $hotels, 'images/tours/nungwi-beachfront.jpg', 'Zanzibar beachfront');
    }

    private function stay(string $location, string $mealPlan, array $hotels, string $image, string $caption): array
    {
        return [
            'location' => $location,
            'meal_plan' => $mealPlan,
            'tiers' => [
                ['label' => 'Comfort', 'name' => $hotels[0]],
                ['label' => 'Premium', 'name' => $hotels[1]],
                ['label' => 'Luxury', 'name' => $hotels[2]],
            ],
            'image' => $image,
            'image_caption' => $caption,
        ];
    }

    private function activity(string $label, string $image, string $description): array
    {
        return ['label' => $label, 'optional' => true, 'image' => $image, 'description' => $description];
    }
}
