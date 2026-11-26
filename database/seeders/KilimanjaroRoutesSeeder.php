<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

/**
 * The full set of Kilimanjaro route packages. Only adds packages whose slug doesn't exist yet, so it never
 * overwrites the original three routes or anything edited in the admin. Safe on production:
 *   php artisan db:seed --class=KilimanjaroRoutesSeeder --force
 *
 * Durations count days on the mountain; arrival and departure hotel nights are arranged separately.
 * Prices are starting points to review in the admin (Packages → Kilimanjaro).
 */
class KilimanjaroRoutesSeeder extends Seeder
{
    private const INCLUDED = ['Park, camping and rescue fees', 'Professional mountain guides, cook and porters', 'All meals on the mountain', 'Mountain tents, sleeping mats and a dining tent', 'Emergency oxygen and first aid kit', 'Transfers to and from the trailhead'];

    private const INCLUDED_HUTS = ['Park, hut and rescue fees', 'Professional mountain guides, cook and porters', 'All meals on the mountain', 'Dormitory-style mountain huts with bunks and mattresses', 'Emergency oxygen and first aid kit', 'Transfers to and from the trailhead'];

    private const EXCLUDED = ['International flights', 'Tanzania visa', 'Hotel nights before and after the climb', 'Tips for guides, cooks and porters', 'Personal climbing gear and sleeping bag'];

    private const BEST_TIME = 'June – October, late December – March';

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
                'category' => 'kilimanjaro',
                'location' => 'Kilimanjaro National Park',
                'group_size_max' => 12,
                'best_time' => self::BEST_TIME,
                'excluded' => self::EXCLUDED,
                'is_featured' => false,
                'sort_order' => 4 + $order,
            ]);

            foreach ($days as $index => $day) {
                $tour->itineraryItems()->create($day + ['day_number' => $index + 1]);
            }

            $created++;
        }

        $this->command?->info($created ? "Added {$created} Kilimanjaro packages." : 'All Kilimanjaro packages already exist, nothing added.');
    }

    private function camp(string $location): array
    {
        return ['location' => $location, 'meal_plan' => 'Full board'];
    }

    private function packages(): array
    {
        return [
            [
                'title' => 'Marangu Route — 5 Days',
                'slug' => 'marangu-route-5-days',
                'route' => 'marangu',
                'summary' => 'The quickest way to Uhuru Peak, sleeping in mountain huts instead of tents.',
                'description' => "The five-day Marangu climb is the shortest standard itinerary on Kilimanjaro and the only one with hut accommodation the whole way, which makes it popular with climbers who would rather not camp. The gentle gradient and well-kept trail suit first-time trekkers, but the pace leaves little time to acclimatize, so it works best for fit hikers with some altitude experience. If you can spare one more day, our six-day Marangu adds an acclimatization day at Horombo and noticeably improves your chances at the top.",
                'duration_days' => 5,
                'price_from' => 1650,
                'difficulty' => 'Challenging',
                'highlight_stat' => 'Hut accommodation',
                'hero_image' => 'images/tours/marangu-5-days.jpg',
                'highlights' => ['Huts with bunks instead of tents', 'Short side walk to Maundi Crater on day one', 'Views of Mawenzi across the alpine saddle', 'Sunrise on the crater rim at Gilman\'s Point and Uhuru Peak'],
                'included' => self::INCLUDED_HUTS,
                'days' => [
                    ['title' => 'Marangu Gate to Mandara Hut', 'description' => 'After registering at Marangu Gate (1,860 m), walk through montane rainforest, often with blue monkeys in the canopy, to Mandara Hut (2,720 m).', 'image' => 'images/tours/marangu-5-days.jpg', 'image_caption' => 'Mandara Hut', 'accommodation' => $this->camp('Mandara Hut'),
                        'activity' => ['label' => 'Maundi Crater walk', 'optional' => false, 'description' => 'A short afternoon walk to the rim of Maundi Crater for the first wide views over the plains and towards Kenya.']],
                    ['title' => 'Mandara Hut to Horombo Hut', 'description' => 'The forest gives way to giant heather and moorland as you climb to Horombo Hut (3,720 m), with Kibo and the jagged Mawenzi peak ahead.', 'accommodation' => $this->camp('Horombo Hut')],
                    ['title' => 'Horombo Hut to Kibo Hut', 'description' => 'Cross the wide alpine desert of the saddle between Mawenzi and Kibo to Kibo Hut (4,720 m) at the foot of the summit cone.', 'note' => 'Early dinner and lights-out: the summit push begins around midnight.', 'accommodation' => $this->camp('Kibo Hut')],
                    ['title' => 'Kibo Hut to Uhuru Peak, descend to Horombo Hut', 'description' => "A midnight start up the scree to Gilman's Point on the crater rim, then around to Uhuru Peak (5,895 m) for sunrise, before the long descent back to Horombo Hut.", 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Horombo Hut')],
                    ['title' => 'Horombo Hut to Marangu Gate', 'description' => 'Descend through moorland and forest to Marangu Gate (1,860 m), where your summit certificates are handed out before the drive back to your hotel.'],
                ],
            ],
            [
                'title' => 'Machame Route — 6 Days',
                'slug' => 'machame-route-6-days',
                'route' => 'machame',
                'summary' => 'The classic "Whiskey Route" in six days, for fit hikers short on time.',
                'description' => "Machame's scenery changes almost every day, from rainforest to moorland, the Shira Plateau and the glaciers of the southern icefields. The six-day version follows the same route as our seven-day climb but combines Barranco and Karanga into one day, going straight on to Barafu base camp. It suits fit trekkers who have been to altitude before; if this is your first high mountain, we recommend the seven-day Machame or a Lemosho itinerary.",
                'duration_days' => 6,
                'price_from' => 1950,
                'difficulty' => 'Challenging',
                'highlight_stat' => 'Six days, five climate zones',
                'hero_image' => 'images/tours/machame-6-days.jpg',
                'highlights' => ['Rainforest, moorland, alpine desert and glaciers in one climb', '"Climb high, sleep low" via Lava Tower', 'The Barranco Wall scramble', 'Descent on the quiet Mweka trail'],
                'included' => self::INCLUDED,
                'days' => [
                    ['title' => 'Machame Gate to Machame Camp', 'description' => 'Register at Machame Gate (1,800 m) and climb through thick rainforest to Machame Camp (3,000 m) at the forest edge.', 'image' => 'images/tours/machame-6-days.jpg', 'image_caption' => 'Machame Camp', 'accommodation' => $this->camp('Machame Camp')],
                    ['title' => 'Machame Camp to Shira Camp', 'description' => 'A steep climb through heather and volcanic rock brings you onto the Shira Plateau and Shira Cave Camp (3,840 m), with Kibo close above.', 'accommodation' => $this->camp('Shira Camp')],
                    ['title' => 'Shira Camp to Barranco Camp via Lava Tower', 'description' => 'Climb to the base of Lava Tower (4,630 m) for acclimatization, then drop into the Barranco Valley and its giant groundsels to camp at 3,950 m.', 'map_place' => 'barranco', 'accommodation' => $this->camp('Barranco Camp')],
                    ['title' => 'Barranco Camp over the Barranco Wall to Barafu Camp', 'description' => 'Scramble up the Barranco Wall, cross the Karanga Valley and continue up to Barafu Camp (4,645 m), base camp for the summit.', 'note' => 'A long day. Early dinner and rest before the midnight start.', 'accommodation' => $this->camp('Barafu Camp')],
                    ['title' => 'Barafu Camp to Uhuru Peak, descend to Mweka Camp', 'description' => 'Climb through the night to Stella Point on the crater rim and on to Uhuru Peak (5,895 m) for sunrise, then descend to Mweka Camp (3,100 m).', 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Mweka Camp')],
                    ['title' => 'Mweka Camp to Mweka Gate', 'description' => 'A last walk down through the rainforest to Mweka Gate (1,640 m) for your summit certificates and the drive back to your hotel.'],
                ],
            ],
            [
                'title' => 'Lemosho Route — 7 Days',
                'slug' => 'lemosho-route-7-days',
                'route' => 'lemosho',
                'summary' => 'The scenic western approach across the Shira Plateau, one day shorter than our 8-day Lemosho.',
                'description' => "Lemosho starts on Kilimanjaro's quiet western side and crosses the whole Shira Plateau before joining the southern circuit, which gives it some of the best scenery and the fewest crowds of any route. This seven-day version walks from Mti Mkubwa straight to Shira 2, keeping a good acclimatization profile with the classic Lava Tower day and a Karanga stop before base camp.",
                'duration_days' => 7,
                'price_from' => 2250,
                'difficulty' => 'Moderate',
                'highlight_stat' => 'Quiet western start',
                'hero_image' => 'images/tours/lemosho-7-days.jpg',
                'highlights' => ['Remote rainforest start with few other groups', 'A full day crossing the Shira Plateau', 'Lava Tower acclimatization day', 'Karanga stop before base camp'],
                'included' => self::INCLUDED,
                'days' => [
                    ['title' => 'Londorossi Gate to Mti Mkubwa Camp', 'description' => 'Drive to Londorossi Gate to register, then hike through untouched rainforest to Mti Mkubwa, the "Big Tree" camp (2,780 m).', 'image' => 'images/tours/lemosho-7-days.jpg', 'image_caption' => 'Mti Mkubwa Camp', 'accommodation' => $this->camp('Mti Mkubwa Camp')],
                    ['title' => 'Mti Mkubwa Camp to Shira 2 Camp', 'description' => 'Leave the forest for heather and moorland, then cross the wide Shira Plateau to Shira 2 Camp (3,890 m).', 'accommodation' => $this->camp('Shira 2 Camp')],
                    ['title' => 'Shira 2 Camp to Barranco Camp via Lava Tower', 'description' => 'Climb high to Lava Tower (4,630 m) and descend to Barranco Camp (3,950 m) to sleep lower, the key acclimatization day of the climb.', 'map_place' => 'barranco', 'accommodation' => $this->camp('Barranco Camp')],
                    ['title' => 'Barranco Camp to Karanga Camp', 'description' => 'Scramble up the Barranco Wall, then follow the southern slopes to Karanga Camp (4,040 m).', 'accommodation' => $this->camp('Karanga Camp')],
                    ['title' => 'Karanga Camp to Barafu Camp', 'description' => 'A short climb over rocky ridges to Barafu Camp (4,645 m). Rest, eat and prepare for the summit.', 'note' => 'Early dinner and lights-out: the summit push begins around midnight.', 'accommodation' => $this->camp('Barafu Camp')],
                    ['title' => 'Barafu Camp to Uhuru Peak, descend to Mweka Camp', 'description' => 'Night climb to Stella Point and Uhuru Peak (5,895 m) for sunrise over the glaciers, then a long descent to Mweka Camp (3,100 m).', 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Mweka Camp')],
                    ['title' => 'Mweka Camp to Mweka Gate', 'description' => 'Descend through the rainforest to Mweka Gate (1,640 m), collect your certificates and head back to your hotel.'],
                ],
            ],
            [
                'title' => 'Northern Circuit Route — 8 Days',
                'slug' => 'northern-circuit-route-8-days',
                'route' => 'northern-circuit',
                'summary' => 'The longest, quietest way up Kilimanjaro, circling the mountain\'s remote northern slopes.',
                'description' => "The Northern Circuit starts like Lemosho in the west, then leaves the busy southern trails behind to wrap around Kibo's wild northern side, with views across to Kenya and very few other climbers. Eight days on the mountain give your body plenty of time to adjust, and the summit approach from School Hut is the quietest of any route.",
                'duration_days' => 8,
                'price_from' => 2650,
                'difficulty' => 'Moderate',
                'highlight_stat' => 'Quietest route',
                'hero_image' => 'images/tours/northern-circuit-route.jpg',
                'highlights' => ['360° views as you circle the mountain', 'Remote northern camps, far from the crowds', 'Long acclimatization profile', 'Summit from School Hut via Gilman\'s Point'],
                'included' => self::INCLUDED,
                'days' => [
                    ['title' => 'Londorossi Gate to Mti Mkubwa Camp', 'description' => 'Drive to Londorossi Gate to register and walk through quiet rainforest to Mti Mkubwa, the "Big Tree" camp (2,780 m).', 'image' => 'images/tours/northern-circuit-route.jpg', 'image_caption' => 'Mti Mkubwa Camp', 'accommodation' => $this->camp('Mti Mkubwa Camp')],
                    ['title' => 'Mti Mkubwa Camp to Shira 1 Camp', 'description' => 'Climb out of the forest onto the western edge of the Shira Plateau and Shira 1 Camp (3,510 m).', 'accommodation' => $this->camp('Shira 1 Camp')],
                    ['title' => 'Shira 1 Camp to Moir Hut', 'description' => 'Cross the plateau with an acclimatization detour towards Lava Tower, then turn north to the sheltered valley of Moir Hut (4,200 m).', 'map_place' => 'moir-hut', 'accommodation' => $this->camp('Moir Hut')],
                    ['title' => 'Moir Hut to Buffalo Camp', 'description' => 'Begin the traverse of the northern slopes, with wide views north over the Kenyan plains, to Buffalo Camp (4,000 m).', 'accommodation' => $this->camp('Buffalo Camp')],
                    ['title' => 'Buffalo Camp to Third Cave Camp', 'description' => 'Continue east around the mountain through high alpine desert to Third Cave Camp (3,930 m).', 'accommodation' => $this->camp('Third Cave Camp')],
                    ['title' => 'Third Cave Camp to School Hut', 'description' => 'Climb onto the saddle below Kibo to School Hut (4,710 m), the base for the summit.', 'note' => 'Early dinner and lights-out: the summit push begins around midnight.', 'accommodation' => $this->camp('School Hut')],
                    ['title' => 'School Hut to Uhuru Peak, descend to Millennium Camp', 'description' => "Night climb to Gilman's Point and along the crater rim to Uhuru Peak (5,895 m) for sunrise, then descend the southern side to Millennium Camp (3,820 m).", 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Millennium Camp')],
                    ['title' => 'Millennium Camp to Mweka Gate', 'description' => 'Walk down through heath and rainforest to Mweka Gate (1,640 m) for your certificates and the drive back to your hotel.'],
                ],
            ],
            [
                'title' => 'Lemosho-Shira Route — 6 Days',
                'slug' => 'lemosho-shira-route-6-days',
                'route' => 'lemosho',
                'summary' => 'A short climb that starts high on the Shira Plateau, reached by 4x4.',
                'description' => "On this route a 4x4 takes you most of the way up the western side to the Shira Plateau, so you start walking at around 3,500 m and save the rainforest days. It is a good option when time is tight, but starting high means acclimatization needs care: we only recommend it to hikers who have been to altitude recently or who spend a few nights in the Arusha highlands first.",
                'duration_days' => 6,
                'price_from' => 1990,
                'difficulty' => 'Moderate',
                'highlight_stat' => 'Starts at 3,500 m',
                'hero_image' => 'images/tours/lemosho-shira.jpg',
                'highlights' => ['Drive up to the Shira Plateau', 'Plateau views of Kibo from day one', 'Lava Tower acclimatization day', 'Descent via the Mweka trail'],
                'included' => self::INCLUDED,
                'days' => [
                    ['title' => 'Drive to Shira Gate, hike to Shira 2 Camp', 'description' => 'A 4x4 drive of about four hours via Londorossi Gate brings you to the plateau at around 3,500 m. A gentle afternoon walk leads to Shira 2 Camp (3,890 m).', 'note' => 'Starting this high, walk slowly ("pole pole") and drink plenty of water from the first hour.', 'image' => 'images/tours/lemosho-shira.jpg', 'image_caption' => 'Shira Plateau', 'accommodation' => $this->camp('Shira 2 Camp')],
                    ['title' => 'Shira 2 Camp to Barranco Camp via Lava Tower', 'description' => 'Climb to Lava Tower (4,630 m) for acclimatization, then descend to sleep in the Barranco Valley (3,950 m).', 'map_place' => 'barranco', 'accommodation' => $this->camp('Barranco Camp')],
                    ['title' => 'Barranco Camp to Karanga Camp', 'description' => 'Tackle the Barranco Wall scramble and follow the southern slopes to Karanga Camp (4,040 m).', 'accommodation' => $this->camp('Karanga Camp')],
                    ['title' => 'Karanga Camp to Barafu Camp', 'description' => 'A short climb to Barafu Camp (4,645 m), with time to rest before the summit night.', 'note' => 'Early dinner and lights-out: the summit push begins around midnight.', 'accommodation' => $this->camp('Barafu Camp')],
                    ['title' => 'Barafu Camp to Uhuru Peak, descend to Millennium Camp', 'description' => 'Climb through the night to Stella Point and Uhuru Peak (5,895 m) for sunrise, then descend to Millennium Camp (3,820 m).', 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Millennium Camp')],
                    ['title' => 'Millennium Camp to Mweka Gate', 'description' => 'Descend through the rainforest to Mweka Gate (1,640 m) for your certificates and the transfer back to your hotel.'],
                ],
            ],
            [
                'title' => 'Rongai Route — 6 Days',
                'slug' => 'rongai-route-6-days',
                'route' => 'rongai',
                'summary' => 'The only northern approach: drier, quieter and a strong choice in the rainy season.',
                'description' => "Rongai is the only route that climbs Kilimanjaro from the north, near the Kenyan border. The northern slopes get less rain than the south, which makes it a good choice in the wetter months, and the trail is quieter and gentler than Machame or Umbwe. You camp beneath the jagged Mawenzi peak on day three, then descend on the Marangu side, so you see both faces of the mountain.",
                'duration_days' => 6,
                'price_from' => 1950,
                'difficulty' => 'Moderate',
                'highlight_stat' => 'Best in the rainy season',
                'hero_image' => 'images/tours/rongai.jpg',
                'highlights' => ['Drier northern slopes', 'Camp beside Mawenzi Tarn below the Mawenzi spires', 'Few other climbers', 'Descent on the Marangu side for a different view'],
                'included' => self::INCLUDED,
                'days' => [
                    ['title' => 'Rongai Gate to First Cave Camp', 'description' => 'A scenic drive round the mountain to Rongai Gate (1,950 m), then a gentle climb through pine forest and farmland edges to First Cave Camp (2,650 m).', 'image' => 'images/tours/rongai.jpg', 'image_caption' => 'First Cave Camp', 'accommodation' => $this->camp('First Cave Camp')],
                    ['title' => 'First Cave Camp to Kikelewa Camp', 'description' => 'Climb steadily through moorland towards Mawenzi to Kikelewa Camp (3,660 m).', 'accommodation' => $this->camp('Kikelewa Camp')],
                    ['title' => 'Kikelewa Camp to Mawenzi Tarn Hut', 'description' => 'A short, steep morning to Mawenzi Tarn (4,305 m), a small lake right under the jagged towers of Mawenzi.', 'accommodation' => $this->camp('Mawenzi Tarn Hut'),
                        'activity' => ['label' => 'Mawenzi acclimatization walk', 'optional' => true, 'description' => 'An afternoon walk higher up the Mawenzi ridges to help your body adjust before you sleep.']],
                    ['title' => 'Mawenzi Tarn Hut to Kibo Hut', 'description' => 'Cross the lunar landscape of the saddle between Mawenzi and Kibo to Kibo Hut (4,720 m).', 'note' => 'Early dinner and lights-out: the summit push begins around midnight.', 'accommodation' => $this->camp('Kibo Hut')],
                    ['title' => 'Kibo Hut to Uhuru Peak, descend to Horombo Hut', 'description' => "A midnight start to Gilman's Point and around the crater rim to Uhuru Peak (5,895 m) for sunrise, then down to Horombo Hut (3,720 m).", 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Horombo Hut')],
                    ['title' => 'Horombo Hut to Marangu Gate', 'description' => 'Descend through moorland and forest to Marangu Gate (1,860 m), collect your certificates and transfer back to your hotel.'],
                ],
            ],
            [
                'title' => 'Umbwe Route — 6 Days',
                'slug' => 'umbwe-route-6-days',
                'route' => 'umbwe',
                'summary' => 'The steepest, most direct route to the top, for experienced and strong hikers.',
                'description' => "Umbwe climbs straight up narrow forest ridges on the southern side and joins the main circuit at Barranco after only two days. It is the most direct and demanding way up the mountain and sees very few groups. Because it gains height so quickly, we only recommend it to experienced hikers who are already acclimatized or have climbed at altitude before.",
                'duration_days' => 6,
                'price_from' => 1950,
                'difficulty' => 'Demanding',
                'highlight_stat' => 'Steepest route',
                'hero_image' => 'images/tours/umbwe.jpg',
                'highlights' => ['Steep forest ridges with almost no other groups', 'Fast, direct ascent to Barranco', 'Acclimatization walk above Karanga', 'Summit via Stella Point'],
                'included' => self::INCLUDED,
                'days' => [
                    ['title' => 'Umbwe Gate to Umbwe Cave Camp', 'description' => 'From Umbwe Gate (1,600 m) the trail climbs steeply along forest ridges, using tree roots as steps, to Umbwe Cave Camp (2,900 m).', 'image' => 'images/tours/umbwe.jpg', 'image_caption' => 'Umbwe Cave Camp', 'accommodation' => $this->camp('Umbwe Cave Camp')],
                    ['title' => 'Umbwe Cave Camp to Barranco Camp', 'description' => 'Continue up the ridge above the tree line, with the Western Breach looming ahead, to join the southern circuit at Barranco Camp (3,950 m).', 'accommodation' => $this->camp('Barranco Camp')],
                    ['title' => 'Barranco Camp to Karanga Camp', 'description' => 'Climb the Barranco Wall to Karanga Camp (4,040 m), with an afternoon acclimatization walk a few hundred metres higher.', 'accommodation' => $this->camp('Karanga Camp')],
                    ['title' => 'Karanga Camp to Barafu Camp', 'description' => 'A short climb to Barafu Camp (4,645 m) to rest before the summit night.', 'note' => 'Early dinner and lights-out: the summit push begins around midnight.', 'accommodation' => $this->camp('Barafu Camp')],
                    ['title' => 'Barafu Camp to Uhuru Peak, descend to Millennium Camp', 'description' => 'Night climb to Stella Point and Uhuru Peak (5,895 m) for sunrise, then descend to Millennium Camp (3,820 m).', 'image' => 'images/hero/kilimanjaro.jpg', 'image_caption' => 'Uhuru Peak, sunrise', 'accommodation' => $this->camp('Millennium Camp')],
                    ['title' => 'Millennium Camp to Mweka Gate', 'description' => 'Descend through the rainforest to Mweka Gate (1,640 m) for your summit certificates and the drive back to your hotel.'],
                ],
            ],
        ];
    }
}
