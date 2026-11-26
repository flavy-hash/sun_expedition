<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

/**
 * Luxury and migration safaris, plus two safari-and-beach trips and a Zanzibar honeymoon. Only adds packages
 * whose slug doesn't exist yet, so it never overwrites anything edited in the admin. Safe on production:
 *   php artisan db:seed --class=LuxurySafarisSeeder --force
 *
 * Lodges are "or similar". Prices are per person for a group of six; groups of two pay more (see each
 * package's note). Review them in the admin.
 */
class LuxurySafarisSeeder extends Seeder
{
    private const SAFARI_INCLUDED = [
        'Private 4x4 safari vehicle with a pop-up roof',
        'Professional English-speaking driver-guide',
        'All park and Ngorongoro Crater fees',
        'Accommodation and meals as listed for each day',
        'Game drives and activities as described',
        'Drinking water in the vehicle',
        'Airport transfers',
        'Government taxes and VAT',
    ];

    private const SAFARI_EXCLUDED = [
        'International flights',
        'Tanzania visa',
        'Travel and health insurance',
        'Tips for your guide and lodge staff',
        'Drinks and meals not listed in the itinerary',
        'Optional activities',
        'Accommodation after the safari (we can book extra nights)',
    ];

    private const MIGRATION_TIME = 'July – October for Mara River crossings';

    private const CALVING_TIME = 'January – March, calving season';

    private const DRY_TIME = 'June – October, December – February';

    public function run(): void
    {
        $created = 0;
        $filled = 0;

        foreach ($this->packages() as $order => $package) {
            if ($existing = Tour::where('slug', $package['slug'])->first()) {
                // Seeded before optional activities existed: fill the list in, but never overwrite admin edits.
                if (blank($existing->optional_activities) && filled($package['optional_activities'] ?? null)) {
                    $existing->update(['optional_activities' => $package['optional_activities']]);
                    $filled++;
                }

                continue;
            }

            $days = $package['days'];
            unset($package['days']);

            $tour = Tour::create($package + [
                'group_size_max' => 6,
                'included' => self::SAFARI_INCLUDED,
                'excluded' => self::SAFARI_EXCLUDED,
                'is_featured' => false,
                'sort_order' => 10 + $order,
            ]);

            foreach ($days as $index => $day) {
                $tour->itineraryItems()->create(array_filter($day, fn ($value) => $value !== null) + ['day_number' => $index + 1]);
            }

            $created++;
        }

        $this->command?->info($created ? "Added {$created} safari packages." : 'All safari packages already exist, nothing added.');
        if ($filled) {
            $this->command?->info("Added optional activities to {$filled} existing packages.");
        }
    }

    /** The optional-activity price lists from each package's brochure. */
    private function extras(string ...$keys): array
    {
        $all = [
            'balloon' => ['name' => 'Hot air balloon ride (Serengeti)', 'price' => 'USD 500 per person'],
            'balloon-range' => ['name' => 'Hot air balloon ride (Serengeti)', 'price' => 'USD 500 – 600 per person'],
            'boma' => ['name' => 'Maasai boma visit', 'price' => 'USD 50 per vehicle'],
            'walking' => ['name' => 'Walking safari', 'price' => 'From USD 30 per person'],
            'sundowner' => ['name' => 'Sundowner', 'price' => 'From USD 50 per person'],
            'sundowner-flat' => ['name' => 'Sundowner', 'price' => 'USD 50 per person'],
            'olduvai' => ['name' => 'Olduvai Gorge museum', 'price' => 'USD 36 per person'],
            'materuni' => ['name' => 'Materuni waterfalls', 'price' => 'USD 150 per person'],
        ];

        return array_map(fn (string $key) => $all[$key], $keys);
    }

    private function packages(): array
    {
        return [
            // ---- Northern Circuit safaris --------------------------------------------------------------------
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '4-Day Great Migration Luxury Safari',
                'slug' => '4-day-great-migration-luxury-safari',
                'optional_activities' => $this->extras('balloon-range', 'boma', 'walking', 'sundowner', 'olduvai'),
                'summary' => 'Fly straight to the Northern Serengeti for the Mara River crossings, then drive back through the central plains.',
                'description' => "The fastest way into the heart of the Great Migration. Instead of spending a day on the road, you fly from Arusha to Kogatende in the Northern Serengeti and start game driving on arrival. You'll spend two days along the Mara River, where the herds gather to cross from July to October, then a day in the central Serengeti looking for lion, leopard and cheetah, before driving back to Arusha through the Ngorongoro highlands. Every night is spent in a high-end tented camp or lodge.",
                'duration_days' => 4,
                'price_from' => 3146,
                'difficulty' => 'Luxury',
                'highlight_stat' => 'Fly-in to the Mara River',
                'location' => 'Northern & Central Serengeti',
                'best_time' => self::MIGRATION_TIME,
                'hero_image' => 'images/safaris/mara-river-crossing.jpg',
                'highlights' => ['Flight from Arusha to the Northern Serengeti', 'A chance of Mara River crossings', 'Lion, leopard and cheetah in the central Serengeti', 'Luxury tented camps with all meals and drinks'],
                'included' => [...self::SAFARI_INCLUDED, 'Flight from Arusha to the Northern Serengeti'],
                'days' => [
                    $this->day('Fly to the Northern Serengeti', "We collect you from your Arusha hotel at about 6:30am with a packed breakfast for the short drive to Arusha Airport and a two-hour flight to the Kogatende airstrip. Your guide meets you on the runway and the game drive starts right away, across a quiet corner of the Serengeti of rolling hills, kopjes and riverine forest. Afternoon drive to camp.", 'northern-serengeti', 'images/safaris/mara-river-herds.jpg', 'Northern Serengeti',
                        $this->stay('Northern Serengeti', 'Mara Mara Tented Lodge', 'All inclusive')),
                    $this->day('The Mara River', "A full day along the Mara River with a picnic lunch. In season the herds gather on the banks, sometimes for days, before plunging across past waiting crocodiles. Crossings are never guaranteed, so your guide reads the herds and moves you between crossing points while you wait.", 'northern-serengeti', 'images/safaris/mara-river-crossing.jpg', 'Mara River',
                        $this->stay('Northern Serengeti', 'Mara Mara Tented Lodge', 'All inclusive'), note: 'River crossings are a matter of luck: a herd can wait on the bank for days before it crosses.'),
                    $this->day('Central Serengeti', "Game drive south through the heart of the Serengeti with a packed lunch, into big-cat country around Seronera, where lion prides rest on the kopjes, leopard drape themselves in the sausage trees and cheetah hunt on the open plains. Arrive at your lodge in the late afternoon.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Central Serengeti',
                        $this->stay('Central Serengeti', 'Serengeti Serena Safari Lodge', 'Full board'), activity: $this->balloon()),
                    $this->day('Serengeti to Arusha', "After breakfast, game drive out of the Serengeti and through the Ngorongoro Conservation Area, with a picnic lunch on the way. We drop you at Kilimanjaro International Airport for your flight home, or at your Arusha hotel if you're staying on.", 'arusha', 'images/tours/northern-circuit.jpg', 'Ngorongoro highlands'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '5-Day Ndutu Calving Season Safari',
                'slug' => '5-day-ndutu-calving-season-safari',
                'optional_activities' => $this->extras('balloon', 'boma', 'walking', 'sundowner', 'olduvai'),
                'summary' => 'Tarangire, the Ngorongoro Crater and two days on the Ndutu plains while the wildebeest give birth.',
                'description' => "Between January and March the migrating herds settle on the short-grass plains around Ndutu, in the southern Serengeti, to give birth. Thousands of calves are born every day, and the predators follow close behind, which makes this some of the most dramatic game viewing of the year. This private safari starts with Tarangire's elephants and a full day in the Ngorongoro Crater before two days among the calving herds.",
                'duration_days' => 5,
                'price_from' => 1762,
                'difficulty' => 'Mid-range',
                'highlight_stat' => 'Calving season',
                'location' => 'Tarangire, Ngorongoro & Ndutu',
                'best_time' => self::CALVING_TIME,
                'hero_image' => 'images/blog/great-migration.jpg',
                'highlights' => ['Thousands of wildebeest calves born each day', 'Lion, cheetah and hyena on the hunt', 'A full morning on the Ngorongoro Crater floor', "Tarangire's elephant herds and baobabs"],
                'days' => [
                    $this->arrival('Arusha Planet Lodge & Spa'),
                    $this->tarangire('Farm of Dreams Lodge, Karatu', 'karatu'),
                    $this->crater('Ndutu Safari Lodge', 'Ndutu', 'Then drive west across the highlands to the Ndutu plains for the night.'),
                    $this->day('Ndutu: the calving plains', "Head out at about 6:30am with a cup of coffee to catch sunrise over the plains as the herds wake up. This is the nursery of the migration: newborn calves find their feet within minutes, and lion, cheetah and hyena are never far away. Picnic lunch in the field and a sunset drive back to the lodge.", 'ndutu', 'images/blog/great-migration.jpg', 'Ndutu',
                        $this->stay('Ndutu', 'Ndutu Safari Lodge', 'Full board'), activity: $this->walkingSafari()),
                    $this->day('Ndutu to Arusha', "A final morning game drive around Ndutu, where elephant, giraffe, hyena and big cats are all common, then lunch and the drive back over the Ngorongoro highlands. We drop you at your Arusha hotel or the airport.", 'arusha', 'images/safaris/serengeti-kopje-lioness.jpg', 'Ndutu'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '6-Day Ndutu Calving Migration Safari',
                'slug' => '6-day-ndutu-calving-migration-safari',
                'optional_activities' => $this->extras('balloon', 'boma', 'walking', 'sundowner', 'olduvai'),
                'summary' => 'Follow the calving herds around Ndutu, then continue into the central Serengeti for the big cats.',
                'description' => "The calving season adds an extra layer to a classic Northern Circuit safari. After Tarangire and the Ngorongoro Crater you spend a full day following the herds around Ndutu and the southern Serengeti, where predator activity peaks as the calves are born, then move on to the kopjes and plains of the central Serengeti. It can be adjusted for other months to follow the migration wherever it is.",
                'duration_days' => 6,
                'price_from' => 2226,
                'difficulty' => 'Mid-range',
                'highlight_stat' => 'Predators at their peak',
                'location' => 'Tarangire, Ngorongoro, Ndutu & Serengeti',
                'best_time' => self::CALVING_TIME,
                'hero_image' => 'images/safaris/serengeti-kopje-lioness.jpg',
                'highlights' => ['The wildebeest calving season around Ndutu', 'Cheetah, lion and hyena hunting', 'Ngorongoro Crater game drive', 'The central Serengeti and its kopjes'],
                'days' => [
                    $this->arrival('Arusha Planet Lodge & Spa'),
                    $this->tarangire('Farm of Dreams Lodge, Karatu', 'karatu'),
                    $this->crater('Ndutu Safari Lodge', 'Ndutu', 'Then continue west to Ndutu for the night.'),
                    $this->day('Following the herds at Ndutu', "Leave at 6am for an early drive, back for breakfast, then out again with a packed lunch to follow the migration wherever it is grazing. From December to April the plains become a nursery, and the predators gather to hunt the young calves. Back at the lodge around 5pm.", 'ndutu', 'images/blog/great-migration.jpg', 'Ndutu',
                        $this->stay('Ndutu', 'Ndutu Safari Lodge', 'Full board')),
                    $this->day('Ndutu to the central Serengeti', "Drive north into the Serengeti proper, the \"endless plains\" in Maasai. Watch for the granite kopjes rising out of the grass, favourite lookouts for lion and leopard. Picnic lunch on the way and a night at a tented camp in the centre of the park.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Central Serengeti',
                        $this->stay('Central Serengeti', 'Serengeti Heritage Tented Camp', 'Full board'), activity: $this->balloon()),
                    $this->day('Sunrise drive and return to Arusha', "An early wake-up for a sunrise game drive, when the animals are most active and the light is beautiful. Then drive back to Arusha through the Ngorongoro area.", 'arusha', 'images/tours/northern-circuit.jpg', 'Serengeti sunrise'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '8-Day Royal Wildlife Safari',
                'slug' => '8-day-royal-wildlife-safari',
                'optional_activities' => $this->extras('balloon', 'boma', 'sundowner-flat', 'walking', 'olduvai', 'materuni'),
                'summary' => 'Tarangire, two days in the Serengeti, a night on the crater rim and Lake Manyara, staying in Meliá lodges.',
                'description' => "A complete tour of the Northern Circuit with time in every park and some of the best lodges in each. You start with Tarangire's elephants and baobabs, spend two full days in the Serengeti, sleep on the rim of the Ngorongoro Crater before a full day on its floor, and finish at Lake Manyara, known for its flamingos and tree-climbing lions. It's an unhurried route with early starts when they count and good lodges to come back to.",
                'duration_days' => 8,
                'price_from' => 5250,
                'difficulty' => 'Luxury',
                'highlight_stat' => 'Crater-rim lodge',
                'location' => 'Tarangire, Serengeti, Ngorongoro & Lake Manyara',
                'best_time' => self::DRY_TIME,
                'hero_image' => 'images/safaris/ngorongoro-crater.jpg',
                'highlights' => ['Baobabs over 1,000 years old in Tarangire', 'Two full days in the Serengeti', 'A night on the Ngorongoro Crater rim', 'Tree-climbing lions at Lake Manyara', 'Bush breakfast on a sunrise game drive'],
                'days' => [
                    $this->arrival('Gran Meliá Arusha'),
                    $this->tarangire('Lake Manyara Kilimamoja Lodge', 'lake-manyara'),
                    $this->day('Into the Serengeti', "Climb into the Ngorongoro highlands and stop at the crater viewpoint for 15 minutes or so, then descend onto the Serengeti plains with a game drive all the way. Picnic lunch in the park and an afternoon drive until sunset.", 'serengeti', 'images/tours/northern-circuit.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board')),
                    $this->day('Full day in the Serengeti', "A full day searching for the Big Five and the more elusive animals like leopard, with a picnic lunch at a scenic spot. In the right months you'll be among the Great Migration herds.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board'), activity: $this->balloon()),
                    $this->day('Sunrise drive and the crater rim', "Up before sunrise for a game drive while it's still cool and the animals are active, with a full breakfast in the bush around 10am. Then game drive out of the Serengeti to your lodge on the rim of the Ngorongoro Crater.", 'ngorongoro', 'images/safaris/ngorongoro-crater.jpg', 'Crater rim',
                        $this->stay('Ngorongoro Crater rim', 'Ngorongoro Lodge, Meliá Collection', 'Full board'), activity: $this->sundowner()),
                    $this->day('Ngorongoro Crater', "Descend to the crater floor for a full day: lion, hyena, elephants with huge tusks and critically endangered black rhino, all in a single volcanic bowl. Lunch at the hippo pool picnic site.", 'ngorongoro', 'images/safaris/ngorongoro-black-rhino.jpg', 'Black rhino',
                        $this->stay('Lake Manyara', 'Lake Manyara Kilimamoja Lodge', 'Full board')),
                    $this->day('Lake Manyara', "A short drive to Lake Manyara National Park, home to flamingos, more than 400 bird species, big elephant herds and the famous tree-climbing lions. Lunch on the lakeshore, then drive back to Arusha for the night.", 'lake-manyara', 'images/safaris/manyara-tree-lion.jpg', 'Lake Manyara',
                        $this->stay('Arusha', 'Gran Meliá Arusha', 'Full board')),
                    $this->day('Departure', "A free day at the hotel before we take you to the airport. If your flight is late, we can add a waterfall walk or the Chemka hot springs.", 'kilimanjaro-airport', 'images/stays/arusha-garden-lodge.jpg', 'Arusha', note: 'A day room is included. Meals today are not.'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '8-Day Luxury Wildlife & Cultural Safari',
                'slug' => '8-day-luxury-wildlife-and-cultural-safari',
                'summary' => 'Two days in Tarangire, a Maasai village, the Ngorongoro Crater, Olduvai Gorge and the Serengeti, flying back to Arusha.',
                'description' => "This safari slows down to take in culture as well as wildlife. You spend two full days in Tarangire, visit a Maasai village on the way to the Ngorongoro Crater, stop at Olduvai Gorge, where some of the earliest human fossils were found, and finish with two days in the Serengeti before a short flight back to Arusha. The lodges are some of the best in each area, including a treetop lodge in Tarangire and a crater-rim lodge at Ngorongoro.",
                'duration_days' => 8,
                'price_from' => 3552,
                'difficulty' => 'Luxury',
                'highlight_stat' => 'Maasai & Olduvai visits',
                'location' => 'Tarangire, Ngorongoro & Serengeti',
                'best_time' => self::DRY_TIME,
                'hero_image' => 'images/safaris/maasai-dancers.jpg',
                'highlights' => ['Two full days in Tarangire', 'Maasai village visit with songs and dancing', 'Full day in the Ngorongoro Crater', 'Olduvai Gorge museum', 'Flight from the Serengeti back to Arusha'],
                'included' => [...self::SAFARI_INCLUDED, 'Maasai village and Olduvai Gorge visits', 'Flight from the Serengeti to Arusha'],
                'days' => [
                    $this->arrival('Gran Meliá Arusha'),
                    $this->day('Arusha to Tarangire', "Drive to Tarangire for an afternoon game drive and a picnic lunch. Elephants, zebra, buffalo and oryx come here from Lake Manyara in search of water, lions follow them, and more bird species breed here than almost anywhere else.", 'tarangire', 'images/safaris/tarangire-elephants.jpg', 'Tarangire',
                        $this->stay('Tarangire', 'Tarangire Kuro Treetops Lodge', 'Full board')),
                    $this->day('Full day in Tarangire', "A morning game drive through grassy plains, swamps and baobab forest, back to the lodge for a hot lunch, then the afternoon to relax with views over the park or head out again.", 'tarangire', 'images/tours/tarangire-manyara.jpg', 'Tarangire',
                        $this->stay('Tarangire', 'Tarangire Kuro Treetops Lodge', 'Full board')),
                    $this->day('Tarangire, a Maasai village and the crater rim', "A last morning drive along the Tarangire River, then on towards Ngorongoro with a stop at a Maasai village to see their dances and songs and the beadwork the women are known for. Arrive at your lodge on the crater rim in the late afternoon.", 'ngorongoro', 'images/safaris/maasai-dancers.jpg', 'Maasai village',
                        $this->stay('Ngorongoro Crater rim', 'Ngorongoro Lodge, Meliá Collection', 'Full board'), activity: $this->maasai(included: true)),
                    $this->day('Ngorongoro Crater', "A full day on the floor of the largest intact crater in the world, 20 km across and home to an estimated 30,000 large mammals: black rhino, black-maned lions, flamingos on the soda lake, and hippo at the picnic pool where you stop for lunch.", 'ngorongoro', 'images/safaris/ngorongoro-black-rhino.jpg', 'Ngorongoro Crater',
                        $this->stay('Ngorongoro Crater rim', 'Ngorongoro Lodge, Meliá Collection', 'Full board')),
                    $this->day('Olduvai Gorge and the Serengeti', "Drive west to Olduvai Gorge, the archaeological site where fossils of our ancestors more than 1.75 million years old were found, and visit the museum. Continue into the Serengeti for an afternoon game drive.", 'olduvai-gorge', 'images/safaris/olduvai-gorge.jpg', 'Olduvai Gorge',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board'), activity: $this->olduvai(included: true)),
                    $this->day('Full day in the Serengeti', "Morning and afternoon game drives with a picnic lunch, among the herds of wildebeest and zebra and the predators that follow them, from open savannah to woodland.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board'), activity: $this->balloon()),
                    $this->day('Fly to Arusha and depart', "After breakfast take a morning flight back to Arusha, with a day room to relax in before your transfer to Kilimanjaro International Airport.", 'kilimanjaro-airport', 'images/stays/arusha-garden-lodge.jpg', 'Arusha', note: 'A day room at Gran Meliá Arusha is included. Meals today are not, so you can order what you like.'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '8-Day Ultimate Wildlife & Luxury Safari',
                'slug' => '8-day-ultimate-wildlife-and-luxury-safari',
                'summary' => 'The Ngorongoro Crater first, then three days in the Serengeti and a final night in Tarangire.',
                'description' => "This route runs the Northern Circuit in reverse to avoid the crowds: straight to the Ngorongoro Crater on day two, then a Maasai village and Olduvai Gorge on the way to three nights in the central Serengeti. You come back through the highlands to Karatu and finish with Tarangire's elephants. Private game drives and top-end lodges throughout.",
                'duration_days' => 8,
                'price_from' => 5669,
                'difficulty' => 'Luxury',
                'highlight_stat' => '3 nights in the Serengeti',
                'location' => 'Ngorongoro, Serengeti & Tarangire',
                'best_time' => self::DRY_TIME,
                'hero_image' => 'images/safaris/serengeti-kopje-lion.jpg',
                'highlights' => ['The Big Five in the Ngorongoro Crater', 'Maasai boma and Olduvai Gorge', 'Three nights in the central Serengeti', 'Tarangire elephants and baobabs'],
                'included' => [...self::SAFARI_INCLUDED, 'Maasai village and Olduvai Gorge visits'],
                'days' => [
                    $this->arrival('Gran Meliá Arusha'),
                    $this->crater('Ngorongoro Lodge, Meliá Collection', 'Ngorongoro Crater rim', 'Then back up to your lodge on the crater rim.', 'Arusha to the Ngorongoro Crater', 'Your private guide collects you after breakfast for the drive to the Ngorongoro Conservation Area and straight down into the crater.'),
                    $this->day('Maasai boma, Olduvai Gorge and the Serengeti', "Visit a nearby Maasai boma to learn about their traditions, ceremonies and how they live alongside the wildlife, then stop at Olduvai Gorge, where Mary and Louis Leakey found early human fossils in 1959. Picnic lunch, then on into the Serengeti.", 'olduvai-gorge', 'images/safaris/olduvai-gorge.jpg', 'Olduvai Gorge',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board'), activity: $this->maasai(included: true)),
                    $this->day('Full day in the central Serengeti', "The Seronera River valley has superb game viewing all year thanks to its resident animals, and it's best in the dry season from June to November when they gather near water. Expect impala, buffalo, hippo, topi, giraffe, lion and leopard.", 'serengeti', 'images/safaris/serengeti-kopje-lioness.jpg', 'Seronera',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board'), activity: $this->balloon()),
                    $this->day('Serengeti full day', "Another full day on the plains with a picnic lunch. Around 500 bird species are recorded here, from ostriches and secretary birds to black eagles.", 'serengeti', 'images/tours/northern-circuit.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board')),
                    $this->day('Serengeti to Karatu', "A final morning game drive in the Serengeti, then back through the Ngorongoro highlands to a mountain lodge near Karatu.", 'karatu', 'images/stays/ngorongoro-highlands-farmhouse.jpg', 'Karatu',
                        $this->stay('Karatu', "Ngorongoro O'ldeani Mountain Lodge", 'Full board')),
                    $this->tarangire('Tarangire Kuro Treetops Lodge', 'tarangire', 'Game drive in Tarangire'),
                    $this->day('Tarangire to Arusha', "Drive back to Arusha after breakfast and relax in a day room before your transfer to Kilimanjaro International Airport.", 'kilimanjaro-airport', 'images/stays/arusha-garden-lodge.jpg', 'Arusha', note: 'A day room at Gran Meliá Arusha is included. Meals today are not.'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '9-Day Great Migration Luxury Safari',
                'slug' => '9-day-great-migration-luxury-safari',
                'summary' => 'Tarangire, Lake Manyara, two days on the Mara River, a walking safari and the Ngorongoro Crater.',
                'description' => "Our most complete migration safari. You start in Tarangire and Lake Manyara, then head deep into the Northern Serengeti for two full days along the Mara River, where the herds cross from July to October. A walking safari with an armed ranger, a Maasai boma, Olduvai Gorge and a final day in the Ngorongoro Crater complete the route, with luxury lodges and camps every night.",
                'duration_days' => 9,
                'price_from' => 6705,
                'difficulty' => 'Luxury',
                'highlight_stat' => 'Two days on the Mara River',
                'location' => 'Tarangire, Manyara, Serengeti & Ngorongoro',
                'best_time' => self::MIGRATION_TIME,
                'hero_image' => 'images/safaris/mara-river-herds.jpg',
                'highlights' => ['Two full days in the Northern Serengeti', 'A chance of Mara River crossings', 'Walking safari with an armed ranger', 'Tree-climbing lions at Lake Manyara', 'Maasai boma, Olduvai Gorge and the Ngorongoro Crater'],
                'included' => [...self::SAFARI_INCLUDED, 'Walking safari, Maasai boma and Olduvai Gorge visits'],
                'days' => [
                    $this->arrival('Gran Meliá Arusha'),
                    $this->tarangire('Tarangire Kuro Treetops Lodge', 'tarangire'),
                    $this->day('Lake Manyara', "Lake Manyara sits below the Rift Valley wall and is famous for tree-climbing lions and more than 450 bird species, including pink flamingos on the soda lake. Picnic lunch and game drive until evening.", 'lake-manyara', 'images/safaris/manyara-tree-lion.jpg', 'Lake Manyara',
                        $this->stay('Lake Manyara', 'Lake Manyara Serena Safari Lodge', 'Full board')),
                    $this->day('Lake Manyara to the Serengeti', "Leave at about 8am and drive through the Ngorongoro Conservation Area, which you'll explore properly at the end of the trip, and on into the Serengeti with a game drive all afternoon.", 'serengeti', 'images/tours/northern-circuit.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board')),
                    $this->day('North to Kogatende', "A full day's game drive north through the Serengeti to Kogatende, near the Kenyan border, with a picnic lunch on the way. This is the quietest, wildest part of the park.", 'northern-serengeti', 'images/safaris/mara-river-herds.jpg', 'Northern Serengeti',
                        $this->stay('Northern Serengeti', 'Ole Serai Luxury Camp, Kogatende', 'Full board')),
                    $this->day('The Mara River', "A full day around the Mara River. In season the herds mass on the banks before crossing past crocodiles and through strong currents. It can take days for a herd to commit, so be patient. It's worth it.", 'northern-serengeti', 'images/safaris/mara-river-crossing.jpg', 'Mara River',
                        $this->stay('Northern Serengeti', 'Ole Serai Luxury Camp, Kogatende', 'Full board'), note: 'River crossings are never guaranteed.'),
                    $this->day('Walking safari in the Serengeti', "Get out of the vehicle and walk with an armed park ranger, looking at tracks, dung, insects and plants and their medicinal uses, the details you'd miss from the vehicle. Then drive south to the central Serengeti.", 'serengeti', 'images/safaris/serengeti-kopje-lioness.jpg', 'Walking safari',
                        $this->stay('Central Serengeti', 'Meliá Serengeti Lodge', 'Full board'), activity: $this->walkingSafari(included: true)),
                    $this->day('Maasai boma, Olduvai Gorge and the crater rim', "Visit a Maasai boma for traditional dances and stories of how they live and herd, then Olduvai Gorge and its fossil museum, before a night on the Ngorongoro Crater rim.", 'ngorongoro', 'images/safaris/maasai-dancers.jpg', 'Maasai boma',
                        $this->stay('Ngorongoro Crater rim', 'Ngorongoro Lodge, Meliá Collection', 'Full board'), activity: $this->olduvai(included: true)),
                    $this->day('Ngorongoro Crater and departure', "An early descent into the crater for a full day among zebra, buffalo, lion, flamingos, hippo and rare black rhino, with lunch at the hippo pool. Drive back to Arusha in the late afternoon for your flight home or another night in town.", 'kilimanjaro-airport', 'images/safaris/ngorongoro-black-rhino.jpg', 'Ngorongoro Crater'),
                ],
            ],
            [
                'category' => 'safari',
                'circuit' => 'northern',
                'title' => '10-Day Luxury Family Safari — Great Migration',
                'slug' => '10-day-luxury-family-safari',
                'optional_activities' => $this->extras('balloon', 'boma', 'walking', 'sundowner', 'olduvai'),
                'summary' => 'A relaxed family safari through every northern park, finishing with two days on the migration in the far north.',
                'description' => "Made for families and honeymooners who want to see everything without rushing. You start at a coffee plantation lodge in Arusha, spend two nights in Tarangire, a day at Lake Manyara and a day in the Ngorongoro Conservation Area, then cross the Serengeti from the central plains to Kogatende in the far north, where the migration crosses the Mara River from July to October. You fly back to Kilimanjaro from the Serengeti, so there's no long drive at the end.",
                'duration_days' => 10,
                'price_from' => 8193,
                'difficulty' => 'Luxury',
                'highlight_stat' => 'Children under 12 from $5,043',
                'location' => 'Tarangire, Manyara, Ngorongoro & Serengeti',
                'best_time' => self::MIGRATION_TIME,
                'group_size_max' => 4,
                'hero_image' => 'images/safaris/tarangire-elephants.jpg',
                'highlights' => ['Coffee plantation lodge in Arusha', 'Two nights in Tarangire', 'Tree-climbing lions at Lake Manyara', 'The Great Migration in the Northern Serengeti', 'Flight back to Kilimanjaro'],
                'included' => [...self::SAFARI_INCLUDED, 'Flight from the Serengeti to Kilimanjaro International Airport'],
                'days' => [
                    $this->day('Arrival: Arusha Coffee Lodge', "We meet you at Kilimanjaro International Airport and take you to Arusha Coffee Lodge, set among one of Tanzania's largest coffee plantations at the foot of Mount Meru. Plantation cottages from the early 1900s, a pool and a garden terrace, and the smell of fresh coffee at breakfast.", 'arusha', 'images/stays/arusha-garden-lodge.jpg', 'Arusha',
                        $this->stay('Arusha', 'Elewana Arusha Coffee Lodge', 'Bed and breakfast')),
                    $this->day('Into Tarangire', "Leave at 7am for Tarangire, 120 km away. Look for lion and leopard, pythons in the trees and the noisy flocks of yellow-collared lovebirds found only in this part of Tanzania. Dinner and the night at a lodge inside the park.", 'tarangire', 'images/safaris/tarangire-elephants.jpg', 'Tarangire',
                        $this->stay('Tarangire', 'Tarangire Kuro Treetops Lodge', 'All inclusive')),
                    $this->day('Full day in Tarangire', "Elephant herds, lions and the giant baobabs, the \"tree of life\", along the Tarangire River, the park's only water in the dry season. Campfire dinner back at the lodge.", 'tarangire', 'images/tours/tarangire-manyara.jpg', 'Tarangire',
                        $this->stay('Tarangire', 'Tarangire Kuro Treetops Lodge', 'All inclusive')),
                    $this->day('Lake Manyara', "Hemingway called it \"the loveliest lake I had seen in Africa\". More than 400 bird species, the famous tree-climbing lions and impressively tusked elephants in the acacia woodland.", 'lake-manyara', 'images/safaris/manyara-tree-lion.jpg', 'Lake Manyara',
                        $this->stay('Lake Manyara', 'Lake Manyara Serena Safari Lodge', 'Full board')),
                    $this->day('Ngorongoro Conservation Area', "A full day in the highlands along the Rift Valley, centred on the Ngorongoro Crater, where Maasai herders graze their cattle alongside the wildlife.", 'ngorongoro', 'images/safaris/ngorongoro-crater.jpg', 'Ngorongoro',
                        $this->stay('Ngorongoro Crater rim', 'Ngorongoro Serena Safari Lodge', 'Full board'), activity: $this->maasai()),
                    $this->day('Olduvai Gorge and the central Serengeti', "Game drive west via Olduvai Gorge with a picnic lunch and into the Serengeti, home to great herds of buffalo, elephant and giraffe and thousands of eland, topi, impala and gazelle.", 'serengeti', 'images/safaris/olduvai-gorge.jpg', 'Olduvai Gorge',
                        $this->stay('Central Serengeti', 'Ole Serai Luxury Camp, Turner Springs', 'All inclusive'), activity: $this->olduvai()),
                    $this->day('Central Serengeti full day', "A full day on the savannah with a picnic lunch, looking for the big cats and whatever the migration is doing this week.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Ole Serai Luxury Camp, Turner Springs', 'All inclusive'), activity: $this->balloon()),
                    $this->day('North to Kogatende', "Head north tracking the migration trails. From July to October, columns of wildebeest up to 40 km long plunge through the crocodile-filled Mara River.", 'northern-serengeti', 'images/safaris/mara-river-herds.jpg', 'Northern Serengeti',
                        $this->stay('Northern Serengeti', 'Mara Mara Tented Lodge', 'Full board')),
                    $this->day('The Mara River', "A full day in the Northern Serengeti with a picnic lunch, waiting for crossings and exploring the kopjes, woodland and riverine forest.", 'northern-serengeti', 'images/safaris/mara-river-crossing.jpg', 'Mara River',
                        $this->stay('Northern Serengeti', 'Mara Mara Tented Lodge', 'Full board'), note: 'River crossings are never guaranteed.'),
                    $this->day('Fly home from Kogatende', "A last morning game drive and picnic lunch, then transfer to the Kogatende airstrip for the flight to Kilimanjaro International Airport at around 2:45pm, arriving around 5:30pm for your international flight.", 'kilimanjaro-airport', 'images/blog/great-migration.jpg', 'Northern Serengeti'),
                ],
            ],

            // ---- Safari & Zanzibar ---------------------------------------------------------------------------
            [
                'category' => 'zanzibar',
                'title' => '12-Day Premium Luxury Safari & Beach Holiday',
                'slug' => '12-day-premium-luxury-safari-and-beach',
                'summary' => 'A luxury safari through Tarangire, Manyara, the Serengeti and Ngorongoro, then Stone Town and four all-inclusive nights in Nungwi.',
                'description' => "Our premium safari-and-beach trip. Six days on safari take you through Tarangire's elephant country, Lake Manyara's flamingos, two days in the Serengeti and a full afternoon in the Ngorongoro Crater, with luxury lodges and camps throughout. Then you fly to Zanzibar for a night in historic Stone Town and four all-inclusive nights on the beach at Nungwi, one of the few places where you can watch both sunrise and sunset over the ocean.",
                'duration_days' => 12,
                'price_from' => 7067,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => 'All-inclusive beach stay',
                'location' => 'Northern Tanzania & Zanzibar',
                'best_time' => self::DRY_TIME,
                'hero_image' => 'images/tours/nungwi-beach.jpg',
                'highlights' => ['Luxury lodges and camps throughout', 'Flamingos at Lake Manyara', 'Two days in the Serengeti', 'Ngorongoro Crater', 'Stone Town and four all-inclusive nights in Nungwi'],
                'included' => ['Private safari vehicle and professional driver-guide', 'All park fees', 'Accommodation and meals as listed for each day', 'Flight from Arusha to Zanzibar', 'All airport transfers', 'Drinking water every day', 'All taxes and VAT'],
                'excluded' => ['International flights', 'Tanzania visa', 'Travel insurance', 'Tips', 'Hotel nights before or after the tour', 'Meals not listed in the itinerary'],
                'days' => [
                    $this->arrival('Gran Meliá Arusha', 'Arrival in Arusha'),
                    $this->day('Arusha to Tarangire', "Drive out across the Maasai plains, past herders in bright shukas, to Tarangire. From the open roof, look for zebra, wildebeest, buffalo, elephant and giraffe, and lions or leopards resting in the trees. Evening at a luxury lodge with a pool.", 'tarangire', 'images/safaris/tarangire-elephants.jpg', 'Tarangire',
                        $this->stay('Karatu', "Ngorongoro O'ldeani Mountain Lodge", 'Full board')),
                    $this->day('Lake Manyara', "A small but varied park, 70% of it covered by a shallow soda lake that draws thousands of flamingos and more than 500 bird species. Monkeys in the forest, giraffe and elephant on the plains, and with luck lions in the trees.", 'lake-manyara', 'images/safaris/manyara-tree-lion.jpg', 'Lake Manyara',
                        $this->stay('Karatu', "Ngorongoro O'ldeani Mountain Lodge", 'Full board')),
                    $this->day('Into the Serengeti', "Up through the farmland of the Iraqw people and the misty forest of the crater highlands, with a view down into Ngorongoro Crater, before reaching the Serengeti by midday for a long afternoon game drive.", 'serengeti', 'images/tours/northern-circuit.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Ole Serai Luxury Camp, Turner Springs', 'All inclusive')),
                    $this->day('Full day in the Serengeti', "A whole day for the plains: impala, buffalo, crocodile and hippo, the migrating herds when they're nearby, and the resident leopard, cheetah and lion.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Ole Serai Luxury Camp, Turner Springs', 'All inclusive'), activity: $this->balloon()),
                    $this->day('Ngorongoro Crater', "Reach the crater rim before lunch and descend 600 metres to a floor of about 25,000 animals: wildebeest, zebra, gazelle, elephant and, with luck, black rhino. A full afternoon inside before driving back to Arusha.", 'ngorongoro', 'images/safaris/ngorongoro-crater.jpg', 'Ngorongoro Crater',
                        $this->stay('Arusha', 'Gran Meliá Arusha', 'Breakfast and dinner')),
                    $this->day('Fly to Zanzibar: Stone Town', "Transfer to Arusha airport for the flight to Zanzibar and a night in Stone Town with views over the old city. Old-town walks, the spice farms, Prison Island or Jozani Forest can all be arranged.", 'stone-town', 'images/tours/stone-town.jpg', 'Stone Town',
                        $this->stay('Stone Town', 'Zanzibar Serena Hotel', 'Bed and breakfast'), activity: $this->stoneTown()),
                    $this->day('Stone Town to Nungwi', "Transfer 50 km north to Nungwi, a fishing village turned lively beach resort with some of Zanzibar's best sand.", 'nungwi', 'images/tours/nungwi-beachfront.jpg', 'Nungwi', $this->stay('Nungwi', 'Meliá Zanzibar', 'All inclusive')),
                    $this->day('Beach day in Nungwi', 'A day for the turquoise water and white sand. Snorkelling, diving and dhow trips can be booked at the hotel.', 'nungwi', 'images/tours/nungwi-beach.jpg', 'Nungwi', $this->stay('Nungwi', 'Meliá Zanzibar', 'All inclusive')),
                    $this->day('Beach day in Nungwi', 'Watch the sunrise and the sunset over the ocean, one of the few places on the island where you can see both.', 'nungwi', 'images/tours/zanzibar-honeymoon.jpg', 'Sunset in Nungwi', $this->stay('Nungwi', 'Meliá Zanzibar', 'All inclusive')),
                    $this->day('Beach day in Nungwi', 'A last full day by the Indian Ocean.', 'nungwi', 'images/tours/zanzibar-white-sand.jpg', 'Nungwi', $this->stay('Nungwi', 'Meliá Zanzibar', 'All inclusive')),
                    $this->day('Departure', 'A relaxed last morning before your transfer to Zanzibar Airport.', 'zanzibar-airport', 'images/tours/zanzibar-dhow-sunset.jpg', 'Zanzibar'),
                ],
            ],
            [
                'category' => 'zanzibar',
                'title' => '11-Day Best of Tanzania & Zanzibar',
                'slug' => '11-day-best-of-tanzania-and-zanzibar',
                'optional_activities' => $this->extras('balloon', 'boma', 'walking'),
                'summary' => 'Six days in Tarangire, the Serengeti, Ngorongoro and Lake Manyara, then five nights at Stone Town\'s Serena Hotel.',
                'description' => "The best of northern Tanzania in six days, then a beach-and-culture break on Zanzibar. You start in Tarangire, spend two full days in the Serengeti, sleep on the Ngorongoro Crater rim and finish at Lake Manyara, where you can add a two-hour walking safari. Then you fly to Zanzibar for five nights at the Serena Hotel on the Stone Town waterfront, with the old town, spice farms and boat trips on the doorstep.",
                'duration_days' => 11,
                'price_from' => 3675,
                'difficulty' => 'Safari & beach',
                'highlight_stat' => 'Stone Town waterfront',
                'location' => 'Northern Tanzania & Zanzibar',
                'best_time' => self::DRY_TIME,
                'hero_image' => 'images/safaris/serengeti-balloons.jpg',
                'highlights' => ["Tarangire's elephants and baobabs", 'Two full days in the Serengeti', 'A night on the Ngorongoro Crater rim', 'Lake Manyara with an optional walking safari', 'Five nights in Stone Town'],
                'included' => ['Private safari vehicle and professional driver-guide', 'All park fees for non-residents', 'Accommodation, meals and drinks as listed', 'Flight from Arusha to Zanzibar', 'Airport transfers', 'All taxes and VAT'],
                'excluded' => ['International flights', 'Tanzania visa', 'Travel insurance', 'Tips', 'Hotel nights before or after the tour', 'Meals not listed in the itinerary', 'Optional activities'],
                'days' => [
                    $this->day('Arusha to Tarangire', "We pick you up from your Arusha hotel at 8am for the 2.5-hour drive to Tarangire, famous for elephants, baobabs and tree-climbing pythons. Game drive until evening.", 'tarangire', 'images/safaris/tarangire-elephants.jpg', 'Tarangire',
                        $this->stay('Karatu', 'Marera Valley Lodge', 'Full board')),
                    $this->day('Into the Serengeti', "A few hours' drive to the Serengeti gate, a picnic lunch and an afternoon game drive across the endless plains, home to a migration of some 1.5 million animals.", 'serengeti', 'images/tours/northern-circuit.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Serengeti Heritage Tented Camp', 'Full board')),
                    $this->day('Full day in the Serengeti', "A whole day of game drives among impala, buffalo, hippo and crocodile, the migrating herds when they're here, and the resident leopard, cheetah and lion.", 'serengeti', 'images/safaris/serengeti-kopje-lion.jpg', 'Serengeti',
                        $this->stay('Central Serengeti', 'Serengeti Heritage Tented Camp', 'Full board'), activity: $this->balloon()),
                    $this->day('Serengeti to the crater rim', "A last morning on the plains, among kopjes, woodland and more than 500 bird species, from Kori bustards to white-backed vultures. Then drive to a lodge on the Ngorongoro Crater rim.", 'ngorongoro', 'images/safaris/serengeti-kopje-lioness.jpg', 'Serengeti',
                        $this->stay('Ngorongoro Crater rim', 'Ngorongoro Rhino Lodge', 'Full board'), activity: $this->maasai()),
                    $this->day('Ngorongoro Crater', "An early descent into the largest intact crater in the world, home to around 30,000 large mammals including black rhino, hyena, zebra and elephant.", 'ngorongoro', 'images/safaris/ngorongoro-black-rhino.jpg', 'Ngorongoro Crater',
                        $this->stay('Karatu', 'Marera Valley Lodge', 'Full board')),
                    $this->day('Lake Manyara', "A day of panoramic views, waterfalls, clear streams and hot springs at Lake Manyara, then back to Arusha for dinner.", 'lake-manyara', 'images/safaris/manyara-tree-lion.jpg', 'Lake Manyara',
                        $this->stay('Arusha', 'Tulia Boutique Hotel & Spa', 'Breakfast and dinner'), activity: $this->walkingSafari(text: 'A two-hour guided walk on the edge of the park, among giraffe and buffalo seen from a safe distance. From $30 per person.')),
                    $this->day('Fly to Zanzibar: Stone Town', 'Transfer to Arusha airport for the flight to Zanzibar and a relaxed first night overlooking the old city.', 'stone-town', 'images/tours/stone-town.jpg', 'Stone Town',
                        $this->stay('Stone Town', 'Zanzibar Serena Hotel', 'Bed and breakfast'), activity: $this->stoneTown()),
                    $this->day('Zanzibar', 'A full day at leisure. Zanzibar has been shaped by Persians, Omani Arabs, Indians, Portuguese and British over the centuries, and it shows in the food, the architecture and the people.', 'stone-town', 'images/tours/zanzibar-dhow-sunset.jpg', 'Zanzibar',
                        $this->stay('Stone Town', 'Zanzibar Serena Hotel', 'Bed and breakfast'), activity: $this->zanzibarExcursion('SPICE TOUR', 'images/tours/zanzibar-spice-farm.jpg', 'A half-day tour of the spice farms north of Stone Town.')),
                    $this->day('Zanzibar', 'Spend the day on the beach, or arrange snorkelling and other water sports through the hotel.', 'stone-town', 'images/tours/zanzibar-white-sand.jpg', 'Zanzibar',
                        $this->stay('Stone Town', 'Zanzibar Serena Hotel', 'Bed and breakfast'), activity: $this->zanzibarExcursion('PRISON ISLAND', 'images/tours/prison-island-tortoises.jpg', 'A short boat trip to see the giant tortoises and snorkel off the island.')),
                    $this->day('Zanzibar', 'A last full day on the island. Jozani Forest and its red colobus monkeys make a good half-day trip.', 'stone-town', 'images/tours/jozani-colobus.jpg', 'Jozani Forest',
                        $this->stay('Stone Town', 'Zanzibar Serena Hotel', 'Bed and breakfast'), activity: $this->zanzibarExcursion('JOZANI FOREST', 'images/tours/jozani-colobus.jpg', 'A guided walk among the red colobus monkeys.')),
                    $this->day('Departure', 'Breakfast and a last stroll before an afternoon transfer from Stone Town to the airport.', 'zanzibar-airport', 'images/tours/stone-town.jpg', 'Stone Town'),
                ],
            ],
            [
                'category' => 'zanzibar',
                'title' => '7-Day Zanzibar Beach Honeymoon',
                'slug' => '7-day-zanzibar-beach-honeymoon',
                'summary' => 'Stone Town, Prison Island, Jozani Forest and a spice tour, with beach days at Warere Beach near Nungwi.',
                'description' => "A honeymoon that mixes relaxed sightseeing with plenty of time on the beach. From your hotel near Nungwi you'll explore Stone Town's slave market, cathedral and palaces and take a boat to Prison Island, walk among the red colobus monkeys of Jozani Forest and meet the villagers of Jambiani, and taste your way around a spice farm at Kizimbani. The last two days are yours to spend on the beach or the water.",
                'duration_days' => 7,
                'price_from' => 1341,
                'difficulty' => 'Honeymoon',
                'highlight_stat' => 'Three guided tours included',
                'location' => 'Zanzibar',
                'best_time' => self::DRY_TIME,
                'hero_image' => 'images/tours/zanzibar-honeymoon.jpg',
                'highlights' => ['Stone Town tour and a boat to Prison Island', 'Jozani Forest and Jambiani village', 'Spice tour at Kizimbani', 'Two free beach days'],
                'included' => ['All accommodation', 'Daily breakfast', 'Guided tours with a professional driver-guide', 'Round-trip airport transfers', 'All taxes and VAT', 'Drinking water'],
                'excluded' => ['International flights', 'Tanzania visa', 'Travel insurance', 'Tips', 'Lunch and dinner', 'Water sports and boat trips on beach days'],
                'days' => [
                    $this->day('Arrival in Zanzibar', 'We meet you at Zanzibar Airport and take you to your beach hotel to settle in.', 'zanzibar-airport', 'images/tours/zanzibar-beach.jpg', 'Zanzibar',
                        $this->stay('Warere Beach', 'Warere Beach Hotel', 'Bed and breakfast')),
                    $this->day('Stone Town and Prison Island', "A guided walk through the old town: the former slave market and the Anglican cathedral built on its site in 1874, the Palace Museum, the House of Wonders and the Old Fort, with carved doors and busy markets in between. Lunch at a restaurant of your choice, then a half-hour boat ride to Changuu (Prison) Island.", 'stone-town', 'images/tours/stone-town.jpg', 'Stone Town',
                        $this->stay('Warere Beach', 'Warere Beach Hotel', 'Bed and breakfast'), activity: $this->zanzibarExcursion('PRISON ISLAND', 'images/tours/prison-island-tortoises.jpg', 'Giant Aldabra tortoises, a small beach and good snorkelling, 5 km off Stone Town.', false), note: 'Lunch today is at your own cost.'),
                    $this->day('Jozani Forest and Jambiani village', "Spend two hours or more in Jozani Forest, one of the last homes of the red colobus monkey, then visit Jambiani village to meet a traditional herbalist, see crops grown on coral stone and the seaweed farms run by local women.", 'jozani', 'images/tours/jozani-colobus.jpg', 'Jozani Forest',
                        $this->stay('Warere Beach', 'Warere Beach Hotel', 'Bed and breakfast')),
                    $this->day('Spice tour at Kizimbani', "More than forty valuable spices and fruits grow on the farms north of Stone Town. Smell and taste cardamom, cinnamon, cloves, nutmeg and black pepper, see the henna bush and lipstick tree, and try jackfruit, lychees and carambola when they're in season.", 'spice-farm', 'images/tours/zanzibar-spice-farm.jpg', 'Kizimbani',
                        $this->stay('Warere Beach', 'Warere Beach Hotel', 'Bed and breakfast')),
                    $this->day('Beach day at Warere', 'Relax by the pool or on the private beach, close to Nungwi. Snorkelling, diving, boat cruises and bird watching can all be arranged.', 'nungwi', 'images/tours/nungwi-beach.jpg', 'Warere Beach',
                        $this->stay('Warere Beach', 'Warere Beach Hotel', 'Bed and breakfast'), activity: $this->zanzibarExcursion('SUNSET DHOW CRUISE', 'images/tours/zanzibar-honeymoon.jpg', 'A two-hour evening sail on a traditional dhow.')),
                    $this->day('Beach day at Warere', 'Another day just for the two of you.', 'nungwi', 'images/tours/zanzibar-white-sand.jpg', 'Warere Beach',
                        $this->stay('Warere Beach', 'Warere Beach Hotel', 'Bed and breakfast')),
                    $this->day('Departure', 'After breakfast we take you to the airport for your flight home.', 'zanzibar-airport', 'images/tours/zanzibar-dhow-sunset.jpg', 'Zanzibar'),
                ],
            ],
        ];
    }

    // ---- Shared days -----------------------------------------------------------------------------------------

    private function arrival(string $hotel, string $title = 'Arrival in Arusha'): array
    {
        return $this->day($title, 'We meet you at Kilimanjaro International Airport and drive you to your hotel in Arusha to rest and get ready for the safari.', 'kilimanjaro-airport', 'images/stays/arusha-garden-lodge.jpg', 'Arusha',
            $this->stay('Arusha', $hotel, 'Bed and breakfast'));
    }

    private function tarangire(string $hotel, string $place, string $title = 'Arusha to Tarangire'): array
    {
        $location = match ($place) {
            'karatu' => 'Karatu',
            'lake-manyara' => 'Lake Manyara',
            default => 'Tarangire',
        };

        return $this->day($title, "Pickup at about 8am for the 2.5-hour drive to Tarangire, famous for its elephant herds and baobab trees, some over a thousand years old. Zebra, giraffe, buffalo, lion and cheetah are common too. Picnic lunch by the Tarangire River and a game drive until evening.", 'tarangire', 'images/safaris/tarangire-elephants.jpg', 'Tarangire',
            $this->stay($location, $hotel, 'Full board'));
    }

    private function crater(string $hotel, string $location, string $after, string $title = 'Ngorongoro Crater', string $before = 'Leave early with a picnic lunch and descend into the Ngorongoro Crater.'): array
    {
        return $this->day($title, "{$before} The caldera is about 3 million years old and 20 km across, the largest unbroken volcanic caldera in the world, and all of the Big Five live on its floor. You'll have at least six hours of game drives inside. {$after}", 'ngorongoro', 'images/safaris/ngorongoro-crater.jpg', 'Ngorongoro Crater',
            $this->stay($location, $hotel, 'Full board'));
    }

    // ---- Optional activities ---------------------------------------------------------------------------------

    private function balloon(): array
    {
        return ['label' => 'HOT-AIR BALLOON SAFARI', 'optional' => true, 'image' => 'images/safaris/serengeti-balloons.jpg', 'description' => 'Lift off at dawn for an hour over the Serengeti, followed by a champagne bush breakfast. From $500 per person.'];
    }

    private function walkingSafari(bool $included = false, ?string $text = null): array
    {
        return ['label' => 'WALKING SAFARI', 'optional' => ! $included, 'image' => 'images/safaris/serengeti-kopje-lioness.jpg', 'description' => $text ?? ($included ? 'A guided walk with an armed park ranger.' : 'A guided walk with an armed ranger. From $30 per person.')];
    }

    private function maasai(bool $included = false): array
    {
        return ['label' => 'MAASAI BOMA VISIT', 'optional' => ! $included, 'image' => 'images/safaris/maasai-dancers.jpg', 'description' => 'Visit a Maasai homestead for traditional songs and dancing, beadwork and stories of life alongside the wildlife.' . ($included ? '' : ' $50 per vehicle.')];
    }

    private function olduvai(bool $included = false): array
    {
        return ['label' => 'OLDUVAI GORGE', 'optional' => ! $included, 'image' => 'images/safaris/olduvai-gorge.jpg', 'description' => 'The museum and viewpoint at the site where some of the earliest human fossils were found.' . ($included ? '' : ' $36 per person.')];
    }

    private function sundowner(): array
    {
        return ['label' => 'SUNDOWNER', 'optional' => true, 'image' => 'images/safaris/ngorongoro-crater.jpg', 'description' => 'Drinks and snacks at a viewpoint as the sun goes down. From $50 per person.'];
    }

    private function stoneTown(): array
    {
        return $this->zanzibarExcursion('STONE TOWN TOUR', 'images/tours/stone-town.jpg', 'A guided walk through the old town, its markets and monuments.');
    }

    private function zanzibarExcursion(string $label, string $image, string $description, bool $optional = true): array
    {
        return ['label' => $label, 'optional' => $optional, 'image' => $image, 'description' => $description];
    }

    // ---- Building blocks -------------------------------------------------------------------------------------

    private function day(string $title, string $description, string $place, string $image, string $caption, ?array $stay = null, ?array $activity = null, ?string $note = null): array
    {
        return [
            'title' => $title,
            'description' => $description,
            'image' => $image,
            'image_caption' => $caption,
            'map_place' => $place,
            'note' => $note,
            'activity' => $activity,
            'accommodation' => $stay,
        ];
    }

    private function stay(string $location, string $hotel, string $mealPlan): array
    {
        return [
            'location' => $location,
            'meal_plan' => $mealPlan,
            'tiers' => [['label' => null, 'name' => "{$hotel} or similar"]],
        ];
    }
}
