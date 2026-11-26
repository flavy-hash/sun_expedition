<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

/**
 * Day trips and short excursions around Arusha and Moshi. Only adds trips whose slug doesn't exist yet, so it
 * never overwrites anything edited in the admin. Safe on production:
 *   php artisan db:seed --class=DayTripsSeeder --force
 *
 * Prices are per person for a group of five or more; small groups pay more per person. Review them in the
 * admin (Packages → Day Trips).
 */
class DayTripsSeeder extends Seeder
{
    private const GUIDE_TIPS = 'Tips for your guide (around $30–50 per vehicle per day)';

    public function run(): void
    {
        $created = 0;

        foreach ($this->trips() as $order => $trip) {
            if (Tour::where('slug', $trip['slug'])->exists()) {
                continue;
            }

            $days = $trip['days'];
            unset($trip['days']);

            $tour = Tour::create($trip + [
                'category' => 'day-trips',
                'duration_days' => 1,
                'best_time' => 'All year',
                'is_featured' => false,
                'sort_order' => 1 + $order,
            ]);

            foreach ($days as $index => $day) {
                $tour->itineraryItems()->create($day + ['day_number' => $index + 1]);
            }

            $created++;
        }

        $this->command?->info($created ? "Added {$created} day trips." : 'All day trips already exist, nothing added.');
    }

    private function trips(): array
    {
        return [
            // ---- From Moshi & Kilimanjaro --------------------------------------------------------------------
            [
                'title' => 'Materuni Waterfall & Coffee Farm Tour',
                'slug' => 'materuni-waterfall-coffee-tour',
                'departs_from' => 'moshi',
                'summary' => 'Walk to an 80-metre waterfall on Kilimanjaro\'s slopes, then roast and brew coffee with a Chagga family.',
                'description' => "Materuni is a Chagga village in the green foothills of Kilimanjaro, about 40 minutes above Moshi. A local guide walks you through banana and avocado farms to a waterfall that drops into a cold pool you can swim in. Back in the village, a family shows you how they grow coffee and take it from bean to cup, roasting and grinding by hand, usually with singing, before a traditional Chagga lunch. It's a great way to stretch your legs before a climb or recover after one.",
                'duration_hours' => '5–6',
                'price_from' => 91,
                'difficulty' => 'Moderate',
                'highlight_stat' => 'Waterfall swim',
                'location' => 'Materuni, Kilimanjaro',
                'group_size_max' => 10,
                'is_featured' => true,
                'hero_image' => 'images/day-trips/materuni-waterfall.jpg',
                'highlights' => ['Swim in the pool below Materuni Falls', 'Roast, grind and brew coffee by hand', 'Traditional Chagga lunch', 'Banana, avocado and mango farms with Kilimanjaro views'],
                'included' => ['Hotel pickup and drop-off in Moshi', 'Village, plantation and waterfall fees', 'Local English-speaking guide', 'Traditional lunch', 'Soft drinks and water'],
                'excluded' => ['Tip for the village guide (around $10 per group)', 'Personal expenses'],
                'days' => [[
                    'title' => 'Materuni Falls & the coffee ceremony',
                    'description' => "Pickup from your Moshi hotel around 9am for the drive up to Materuni. The walk to the falls takes about 45 minutes each way on a path that can be steep and slippery, with time for a swim at the bottom. In the afternoon you join a family on their coffee farm to pick, roast and grind beans and drink the result, then sit down to a Chagga lunch before heading back to Moshi.",
                    'image' => 'images/day-trips/materuni-waterfall.jpg',
                    'image_caption' => 'Materuni Falls',
                    'note' => 'Bring swimwear, a towel and shoes with good grip.',
                    'map_place' => 'materuni',
                    'activity' => $this->activity('COFFEE CEREMONY', 'images/day-trips/materuni-waterfall.jpg', 'A hands-on coffee session with a Chagga family: pick, roast and grind the beans, then brew and taste.'),
                ]],
            ],
            [
                'title' => 'Chemka Hot Springs',
                'slug' => 'chemka-hot-springs',
                'departs_from' => 'moshi',
                'summary' => 'Swim in a clear, warm spring-fed pool under fig trees in the middle of the dry savannah.',
                'description' => "Chemka, also called Kikuletwa or Maji Moto, is a spring-fed pool surrounded by palms and fig trees in the dry plains south of Kilimanjaro. The water is warm, clear enough to see the small fish that nibble your feet, and deep enough to jump into from the rope swing. It's an easy, relaxed day and the perfect reward after a climb or a long safari.",
                'duration_hours' => '5–6',
                'price_from' => 85,
                'difficulty' => 'Easy',
                'highlight_stat' => 'Warm natural pool',
                'location' => 'Chemka, near Moshi',
                'group_size_max' => 10,
                'is_featured' => true,
                'hero_image' => 'images/day-trips/chemka-hot-springs.jpg',
                'highlights' => ['Clear, warm spring water', 'Rope swing and snorkelling', 'Picnic lunch in the shade', 'An easy rest day after a climb'],
                'included' => ['Hotel pickup and drop-off in Moshi', 'Entrance fee', 'Picnic lunch with table and chairs', 'Snorkelling mask', 'Towels', 'Soft drinks and water'],
                'excluded' => ['Personal toiletries', 'Tips'],
                'days' => [[
                    'title' => 'A day at Chemka Hot Springs',
                    'description' => "Leave Moshi around 9am for the drive of just under two hours, the last part on dirt roads through sugar cane and maize. Spend the day swimming, snorkelling and relaxing in the shade, with a picnic lunch set up beside the water. Back at your hotel by late afternoon.",
                    'image' => 'images/day-trips/chemka-hot-springs.jpg',
                    'image_caption' => 'Chemka Hot Springs',
                    'note' => 'Bring swimwear and sunscreen. Weekends can be busy with local visitors.',
                    'map_place' => 'chemka',
                ]],
            ],
            [
                'title' => 'Lake Chala Crater Lake',
                'slug' => 'lake-chala-crater-lake',
                'departs_from' => 'moshi',
                'summary' => 'Canoe on a turquoise crater lake on the Kenyan border, with Kilimanjaro views on the way home.',
                'description' => "Lake Chala fills a steep volcanic crater on Kilimanjaro's eastern edge, right on the border with Kenya. You look down on the turquoise water from the crater rim, often with baboons nearby, then walk down to paddle a canoe across the lake. Lunch is a picnic by the shore, and on clear afternoons the drive back gives some of the best views of Kilimanjaro's eastern side.",
                'duration_hours' => '5–6',
                'price_from' => 118,
                'difficulty' => 'Easy',
                'highlight_stat' => 'Canoeing',
                'location' => 'Lake Chala, Kilimanjaro',
                'group_size_max' => 10,
                'hero_image' => 'images/day-trips/lake-chala.jpg',
                'highlights' => ['Panoramic views from the crater rim', 'Canoeing on the lake', 'Baboons and birdlife', 'Kilimanjaro views on the drive back'],
                'included' => ['Hotel pickup and drop-off in Moshi', 'Entrance fee', 'Canoe hire', 'Picnic lunch with table and chairs', 'Soft drinks and water'],
                'excluded' => ['Personal toiletries', 'Tips'],
                'days' => [[
                    'title' => 'Crater rim, canoe and picnic',
                    'description' => "Morning pickup in Moshi for the drive east to Lake Chala, about an hour and a half. Stop on the crater rim for the view, then take the path down to the water for a canoe trip and a picnic lunch by the shore. Head back in the afternoon with Kilimanjaro on your left.",
                    'image' => 'images/day-trips/lake-chala.jpg',
                    'image_caption' => 'Lake Chala',
                    'note' => 'The path down to the lake is steep in places. Swimming isn\'t recommended.',
                    'map_place' => 'lake-chala',
                ]],
            ],
            [
                'title' => 'Kilimanjaro Day Hike to Mandara Hut',
                'slug' => 'kilimanjaro-day-hike',
                'departs_from' => 'moshi',
                'summary' => 'A taste of Africa\'s highest mountain: a rainforest hike on the Marangu route, no summit required.',
                'description' => "Not everyone has a week to climb Kilimanjaro. This day hike takes you up the Marangu route from the park gate through thick montane rainforest, where black-and-white colobus monkeys feed in the canopy, to Mandara Hut at 2,720 metres, the first camp for summit climbers. Families and anyone short on time can feel what it's like to be on the mountain. It can also be stretched to two days with a night at Mandara Hut.",
                'duration_hours' => '8–9',
                'price_from' => 739,
                'difficulty' => 'Moderate',
                'highlight_stat' => 'Up to 2,720 m',
                'location' => 'Kilimanjaro National Park',
                'group_size_max' => 12,
                'hero_image' => 'images/tours/marangu.jpg',
                'highlights' => ['Hike into Kilimanjaro National Park', 'Colobus monkeys in the rainforest', 'Lunch at Mandara Hut (2,720 m)', 'Optional side trip to Maundi Crater on the 2-day version'],
                'included' => ['Hotel pickup and drop-off in Moshi', 'Kilimanjaro National Park fees', 'Licensed mountain guide with first-aid training', 'Picnic lunch and drinks on the trail', 'First-aid kit'],
                'excluded' => ['Accommodation before and after the hike', 'Gear rental', 'Tips for the guide (around $30 per hiker)', 'Travel insurance'],
                'days' => [[
                    'title' => 'Marangu Gate to Mandara Hut and back',
                    'description' => "Early pickup in Moshi for the 45-minute drive to Marangu Gate (1,860 m). After registering with the park, hike for around three hours through rainforest to Mandara Hut, eat lunch with the climbers starting their ascent, then walk back down the same trail to the gate and your hotel.",
                    'image' => 'images/tours/marangu.jpg',
                    'image_caption' => 'Marangu route',
                    'note' => 'Want more time on the mountain? Ask for the 2-day version with a night at Mandara Hut.',
                    'map_place' => 'mandara-hut',
                ]],
            ],
            [
                'title' => 'Ndarakwai Ranch Safari',
                'slug' => 'ndarakwai-ranch-safari',
                'departs_from' => 'moshi',
                'summary' => 'Elephants below Kilimanjaro on a private conservancy, with a walking safari and First World War trenches.',
                'description' => "Ndarakwai is an 11,000-acre private conservancy on the western plains of Kilimanjaro, brought back from overgrazing and poaching since 1995. Elephant, giraffe, zebra, eland and the long-necked gerenuk move between the waterholes, and lion and cheetah have returned. Because it's private land you can get out and walk with an armed ranger, and there are trenches left from First World War fighting in the hills.",
                'duration_hours' => '4–5',
                'price_from' => 191,
                'difficulty' => 'Easy',
                'highlight_stat' => 'Walking safari',
                'location' => 'West Kilimanjaro',
                'group_size_max' => 7,
                'hero_image' => 'images/day-trips/arusha-national-park.jpg',
                'highlights' => ['Elephants with Kilimanjaro behind them', 'Guided walking safari', 'Rare gerenuk antelope', 'More than 300 bird species', 'First World War trench remains'],
                'included' => ['Hotel pickup and drop-off in Moshi', '4x4 Land Cruiser with a pop-up roof', 'English-speaking safari guide', 'Conservancy fees', 'Guided walking safari', 'Picnic lunch, soft drinks and water', 'Binoculars'],
                'excluded' => [self::GUIDE_TIPS],
                'days' => [[
                    'title' => 'Game drive and bush walk at Ndarakwai',
                    'description' => "Drive west from Moshi to the conservancy for a game drive between the waterholes, then set off on foot with a ranger to read tracks and get closer to the smaller things you'd miss from the vehicle. A picnic lunch under the acacias, then back to Moshi.",
                    'image' => 'images/hero/kilimanjaro.jpg',
                    'image_caption' => 'Ndarakwai',
                    'map_place' => 'ndarakwai',
                    'activity' => $this->activity('WALKING SAFARI', 'images/day-trips/arusha-national-park.jpg', 'About an hour on foot with an armed ranger, at an easy pace.'),
                ]],
            ],
            [
                'title' => 'Kilimanjaro Helicopter Tour',
                'slug' => 'kilimanjaro-helicopter-tour',
                'departs_from' => 'moshi',
                'summary' => 'An hour in the air along the slopes of Kilimanjaro, over the climbing routes and Lake Chala.',
                'description' => "See Kilimanjaro the way few people do. The helicopter lifts off from Moshi Airport, flies east over town and along the mountain's flanks above the forest and the climbing routes, then loops over the Lake Chala crater before heading back. Park rules don't allow flights over the summit crater, but the views of the glaciers and the ice fields from the side are spectacular on a clear morning.",
                'duration_hours' => '1',
                'price_from' => 4200,
                'difficulty' => 'Easy',
                'highlight_stat' => '1-hour flight',
                'location' => 'Moshi Airport',
                'group_size_max' => 3,
                'hero_image' => 'images/day-trips/kilimanjaro-aerial.jpg',
                'best_time' => 'Clear mornings, June – October and January – February',
                'highlights' => ['Kilimanjaro\'s glaciers and climbing routes from the air', 'Low pass over Lake Chala', 'Window seat for every passenger'],
                'included' => ['1-hour helicopter flight', 'Drinks and a lunch box'],
                'excluded' => ['Transfers to Moshi Airport (we can arrange them)', 'Flying over the summit crater, which park rules prohibit'],
                'days' => [[
                    'title' => 'Flight over Kilimanjaro',
                    'description' => "Meet at Moshi Airport early in the morning, when the mountain is most often clear, for a safety briefing. The flight lasts about an hour: east over Moshi, along the southern slopes and climbing routes, and around Lake Chala before landing back where you started.",
                    'image' => 'images/day-trips/kilimanjaro-aerial.jpg',
                    'image_caption' => 'Kilimanjaro from the air',
                    'note' => 'Flights depend on the weather and may move to another morning if the mountain is in cloud. The price is per person.',
                    'map_place' => 'moshi-airport',
                ]],
            ],

            // ---- From Arusha -------------------------------------------------------------------------------------
            [
                'title' => 'Arusha National Park Walking Safari',
                'slug' => 'arusha-national-park-walking-safari',
                'departs_from' => 'arusha',
                'summary' => 'Walk with a ranger on the slopes of Mount Meru, then drive to the flamingos on the Momella Lakes.',
                'description' => "Arusha National Park is small, green and just outside town, and it's one of the few parks where you can leave the vehicle and walk. With an armed ranger you follow trails through the forest on the lower slopes of Mount Meru, past buffalo, giraffe and black-and-white colobus monkeys. Then you drive on to the Momella Lakes, where flamingos often turn the shallows pink.",
                'duration_hours' => '4–5',
                'price_from' => 263,
                'difficulty' => 'Easy',
                'highlight_stat' => 'On foot with a ranger',
                'location' => 'Arusha National Park',
                'group_size_max' => 7,
                'is_featured' => true,
                'hero_image' => 'images/day-trips/momella-flamingos.jpg',
                'highlights' => ['Walking safari with an armed ranger', 'Black-and-white colobus monkeys', 'Flamingos on the Momella Lakes', 'Views of Mount Meru'],
                'included' => ['Hotel pickup and drop-off in Arusha (Moshi on request)', 'Walking safari with a park ranger', '4x4 safari vehicle', 'English-speaking guide', 'Park fees', 'Lunch box, soft drinks and water', 'Binoculars'],
                'excluded' => [self::GUIDE_TIPS, 'Alcoholic drinks'],
                'days' => [[
                    'title' => 'Forest walk and the Momella Lakes',
                    'description' => "Pickup at your Arusha hotel for the 40-minute drive to the park gate. Walk with your ranger through forest and open glades towards Mount Meru, then rejoin your vehicle for a game drive to the Momella Lakes and their flamingos. Lunch is a picnic in the park before the drive back.",
                    'image' => 'images/day-trips/momella-flamingos.jpg',
                    'image_caption' => 'Momella Lakes',
                    'map_place' => 'arusha-national-park',
                    'activity' => $this->activity('WALKING SAFARI', 'images/day-trips/arusha-national-park.jpg', 'Around two hours on foot with an armed park ranger, at a gentle pace with plenty of stops.'),
                ]],
            ],
            [
                'title' => 'Tarangire Day Safari',
                'slug' => 'tarangire-day-safari',
                'departs_from' => 'arusha',
                'summary' => 'A full day among Tarangire\'s baobabs and elephant herds, the easiest big park to reach from Arusha.',
                'description' => "Tarangire is two hours from Arusha and famous for two things: ancient baobab trees and big families of elephants that gather along the Tarangire River, especially in the dry season. You'll also see zebra, giraffe, buffalo and often lion, and more than 550 bird species have been recorded here. If you only have one day for a safari, this is the one we recommend.",
                'duration_hours' => '9–10',
                'price_from' => 245,
                'difficulty' => 'Easy',
                'highlight_stat' => 'Elephant herds',
                'location' => 'Tarangire National Park',
                'group_size_max' => 7,
                'hero_image' => 'images/tours/tarangire-manyara.jpg',
                'best_time' => 'June – October for the biggest elephant herds',
                'highlights' => ['Large elephant families along the river', 'Ancient baobab trees', 'Lion, giraffe, zebra and buffalo', 'Over 550 bird species'],
                'included' => ['Hotel pickup and drop-off in Arusha (Moshi on request)', '4x4 Land Cruiser with a pop-up roof', 'English-speaking safari guide', 'Park fees', 'Packed lunch, soft drinks and water', 'Binoculars'],
                'excluded' => [self::GUIDE_TIPS],
                'days' => [[
                    'title' => 'Game drive in Tarangire',
                    'description' => "Early pickup in Arusha for the two-hour drive to Tarangire. Spend the day on game drives along the river and through the baobab country, with a picnic lunch at a viewpoint over the valley. Back at your hotel in the early evening.",
                    'image' => 'images/tours/tarangire-manyara.jpg',
                    'image_caption' => 'Tarangire',
                    'map_place' => 'tarangire',
                ]],
            ],
            [
                'title' => 'Horseback Riding near Arusha',
                'slug' => 'horseback-riding-arusha',
                'departs_from' => 'arusha',
                'summary' => 'Two hours on horseback across open country near Usa River, with zebra and antelope along the way.',
                'description' => "Ride out from Dolly Estate near Usa River, about 40 minutes from Arusha, on dusty paths through open bush where zebra, wildebeest, antelope and sometimes the long-necked gerenuk graze. The horses are calm and well looked after and the guides pick the pace, so it suits complete beginners as well as experienced riders.",
                'duration_hours' => '2',
                'price_from' => 120,
                'difficulty' => 'Easy',
                'highlight_stat' => 'All levels welcome',
                'location' => 'Usa River, Arusha',
                'group_size_max' => 6,
                'hero_image' => 'images/day-trips/mount-meru-slopes.jpg',
                'highlights' => ['Ride among zebra and wildebeest', 'Views of Mount Meru', 'Suitable for beginners'],
                'included' => ['Experienced riding guide', 'Two-hour ride', 'Safety helmets'],
                'excluded' => ['Transfers to the stables (we can arrange them)', 'Meals and drinks', 'Tips'],
                'days' => [[
                    'title' => 'Ride at Dolly Estate',
                    'description' => "Meet at the stables for a short briefing and to get matched with a horse, then head out for two hours across the estate with your guide. The group moves at the pace of the least experienced rider.",
                    'image' => 'images/day-trips/mount-meru-slopes.jpg',
                    'image_caption' => 'Usa River',
                    'note' => 'Wear long trousers, closed shoes with a small heel and neutral colours. Riders with very different experience levels should book separate rides.',
                    'map_place' => 'usa-river',
                ]],
            ],
            [
                'title' => 'Arusha Markets & Shopping Tour',
                'slug' => 'arusha-shopping-tour',
                'departs_from' => 'arusha',
                'summary' => 'The Maasai Market, tanzanite, local design shops and lunch at Arusha Coffee Lodge, with a driver to guide you.',
                'description' => "A relaxed day for finding the things you'll want to take home. Your driver takes you to the Maasai Market for carvings, beadwork and fabrics, to a tanzanite museum to learn about the gem found only near Kilimanjaro, and to small design shops selling handmade homeware. You have lunch on the terrace at Arusha Coffee Lodge, visit Shanga, a workshop that employs people with disabilities to make glass and jewellery from recycled materials, and finish at the Cultural Heritage Centre. We don't take commission from any of the shops.",
                'duration_hours' => '6–7',
                'price_from' => 70,
                'difficulty' => 'Easy',
                'highlight_stat' => 'No commission shops',
                'location' => 'Arusha',
                'group_size_max' => 6,
                'hero_image' => 'images/day-trips/arusha-market.jpg',
                'highlights' => ['Maasai Market crafts straight from the makers', 'Tanzanite museum', 'Shanga workshop', 'Lunch at Arusha Coffee Lodge', 'Cultural Heritage Centre gallery'],
                'included' => ['Driver and vehicle from 9am to 4:30pm', 'Lunch at Arusha Coffee Lodge'],
                'excluded' => ['Anything you buy', 'Tips'],
                'days' => [[
                    'title' => 'Markets, makers and lunch',
                    'description' => "Pickup at 9am. Morning at the Maasai Market and the tanzanite museum, then local design shops before lunch at Arusha Coffee Lodge and a visit to Shanga next door. The afternoon finishes at the Cultural Heritage Centre, and you're back at your hotel by 4:30pm.",
                    'image' => 'images/day-trips/arusha-market.jpg',
                    'image_caption' => 'Arusha',
                    'note' => 'Some shops close on Sundays. Bring cash in small notes for the market.',
                    'map_place' => 'arusha',
                ]],
            ],

            // ---- Overnight adventures ----------------------------------------------------------------------------
            [
                'title' => 'Ol Doinyo Lengai Volcano Climb',
                'slug' => 'ol-doinyo-lengai-climb',
                'departs_from' => 'overnight',
                'duration_days' => 2,
                'summary' => 'A night climb up Tanzania\'s active volcano to watch sunrise from the crater rim above Lake Natron.',
                'description' => "Ol Doinyo Lengai, the Maasai \"Mountain of God\", is the only volcano on Earth that erupts natrocarbonatite lava, which flows black and turns white in the air. The climb is short but brutally steep: you start at 1am to avoid the heat and reach the crater rim at 2,960 metres for sunrise over the Rift Valley and Lake Natron, often hearing the lava bubbling inside. The trip includes a night at Lake Natron with a walk to the Ngare Sero waterfall and flamingos on the lake.",
                'price_from' => 1141,
                'difficulty' => 'Strenuous',
                'highlight_stat' => 'Sunrise at 2,960 m',
                'location' => 'Lake Natron',
                'group_size_max' => 7,
                'hero_image' => 'images/day-trips/ol-doinyo-lengai.jpg',
                'best_time' => 'June – October and December – February',
                'highlights' => ['Sunrise from the crater rim of an active volcano', 'Hear the lava moving inside the crater', 'Flamingos on Lake Natron', 'Ngare Sero waterfall walk', 'Maasai country along the way'],
                'included' => ['Hotel pickup in Arusha and return', 'Climbing permit and Lake Natron community fees', 'One night at Lake Natron Camp, full board', 'Day room and lunch after the climb', '4x4 Land Cruiser with a pop-up roof', 'English-speaking safari guide and a Maasai climbing guide', 'Flying Doctors evacuation cover and first-aid kit'],
                'excluded' => ['Airport transfers', 'Hotel nights in Arusha', 'Porters on the climb', 'Tips for the guides (around $20–50 for the climb)'],
                'days' => [
                    [
                        'title' => 'Arusha to Lake Natron',
                        'description' => "Leave Arusha in the morning for the long drive north through Maasai country to Lake Natron, arriving around lunchtime. In the afternoon walk up the Ngare Sero gorge to the waterfall, wading through the stream, then drive down to the lakeshore to see the flamingos before an early dinner and bed.",
                        'image' => 'images/day-trips/ol-doinyo-lengai.jpg',
                        'image_caption' => 'Lake Natron',
                        'note' => 'Wear sandals you can walk through water in for the waterfall hike.',
                        'map_place' => 'lake-natron',
                        'accommodation' => ['location' => 'Lake Natron', 'meal_plan' => 'Full board', 'tiers' => [['label' => 'Camp', 'name' => 'Lake Natron Camp']], 'image' => 'images/day-trips/ol-doinyo-lengai.jpg', 'image_caption' => 'Lake Natron'],
                        'activity' => $this->activity('NGARE SERO WATERFALL', 'images/day-trips/materuni-waterfall.jpg', 'A guided walk of about two hours up the gorge to the falls, with stream crossings.'),
                    ],
                    [
                        'title' => 'Night climb, crater rim and return to Arusha',
                        'description' => "Your driver takes you to the foot of the volcano at 1am. The climb takes 5–7 hours up loose, steep ash slopes, with a breakfast stop on the way, and you reach the crater rim at 2,960 metres around sunrise. The descent is the hardest part on the knees, so take it slowly. Your driver meets you at the bottom with cold drinks, and after a shower and lunch at camp you drive back to Arusha.",
                        'image' => 'images/day-trips/ol-doinyo-lengai.jpg',
                        'image_caption' => 'Ol Doinyo Lengai',
                        'note' => 'This is a hard climb for fit hikers. Bring a fleece for the cold start, a sun hat, and at least three litres of water.',
                        'map_place' => 'ol-doinyo-lengai',
                    ],
                ],
            ],
        ];
    }

    private function activity(string $label, string $image, string $description): array
    {
        return ['label' => $label, 'optional' => false, 'image' => $image, 'description' => $description];
    }
}
