<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use Illuminate\Database\Seeder;

/**
 * Starter stays so the page isn't empty on day one. Only runs on an empty table, so it never
 * overwrites admin edits and is safe on production: php artisan db:seed --class=AccommodationSeeder --force
 */
class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        if (Accommodation::query()->exists()) {
            $this->command?->info('Accommodations already exist, nothing seeded.');

            return;
        }

        $stays = [
            ['Arusha Garden Lodge', 'Arusha', 'Lodge', 'Budget', 'images/stays/arusha-garden-lodge.jpg',
                'A quiet garden lodge on the edge of Arusha — the first and last night of most safaris and climbs, with an early start and a short drive to the parks.'],
            ['Tarangire Riverside Lodge', 'Tarangire', 'Lodge', 'Mid-range', 'images/stays/tarangire-riverside-lodge.jpg',
                'Overlooks the Tarangire River valley, where elephant herds come down to drink at sunset. Pool, open-air dining and rooms set among baobabs.'],
            ['Ngorongoro Highlands Farmhouse', 'Ngorongoro', 'Farmhouse', 'Mid-range', 'images/stays/ngorongoro-highlands-farmhouse.jpg',
                'A coffee-farm house in the green Karatu highlands, a short drive from the crater gate. Cool evenings, log fires and food from the farm garden.'],
            ['Serengeti Tented Camp', 'Serengeti', 'Tented camp', 'Mid-range', 'images/stays/serengeti-tented-camp.jpg',
                'Canvas tents with proper beds and en-suite bathrooms, placed close to where the migration is that season. Hear lions at night from the comfort of your tent.'],
            ['Serengeti Ridge-top Lodge', 'Serengeti', 'Lodge', 'Luxury', 'images/stays/serengeti-ridge-lodge.jpg',
                'Infinity pool and wide decks above the central Serengeti plains, with game drives starting straight from the lodge.'],
            ['Ruaha Luxury Tented Camp', 'Ruaha', 'Tented camp', 'Luxury', 'images/stays/ruaha-luxury-tented-camp.jpg',
                'Spacious canvas suites under thatch in Ruaha, one of Africa\'s least crowded parks. Walking safaris and night drives available.'],
            ['Stone Town Heritage Hotel', 'Zanzibar', 'Boutique hotel', 'Mid-range', 'images/tours/stone-town.jpg',
                'A restored merchant\'s house in the heart of UNESCO-listed Stone Town, with a rooftop terrace over the old town and the harbour.'],
            ['Nungwi Beachfront Resort', 'Zanzibar', 'Beach resort', 'Luxury', 'images/tours/zanzibar-beach.jpg',
                'Right on the white sand at Nungwi, on Zanzibar\'s northern tip, where the water stays swimmable at every tide and the sunsets are famous.'],
        ];

        foreach ($stays as $order => [$name, $location, $type, $level, $image, $description]) {
            Accommodation::create([
                'name' => $name,
                'location' => $location,
                'type' => $type,
                'level' => $level,
                'image' => $image,
                'description' => $description,
                'is_published' => true,
                'sort_order' => $order + 1,
            ]);
        }
    }
}
