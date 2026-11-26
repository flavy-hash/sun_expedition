<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $posts = [
            [
                'title' => 'Machame vs Lemosho: Which Kilimanjaro Route Should You Choose?',
                'slug' => 'machame-vs-lemosho-which-route',
                'excerpt' => 'Both routes summit Kilimanjaro from the west, but they differ in difficulty, crowds, and acclimatization. Here is how to decide.',
                'body' => "Choosing a Kilimanjaro route is the single biggest decision you'll make before your climb, and Machame versus Lemosho is the comparison we get asked about most.\n\nMachame — the 'Whiskey Route' — is shorter at seven days and steeper in places, including the famous Barranco Wall scramble. It's a fantastic choice if you have solid trekking fitness and want a slightly more compact trip.\n\nLemosho starts further west and adds an extra day crossing the quieter Shira Plateau before joining the same path as Machame. That extra day is entirely about acclimatization, and it shows in the numbers — Lemosho consistently posts a higher summit success rate.\n\nOur honest advice: if your schedule allows it, take the extra day. Altitude, not fitness, is what turns most people back below the summit, and time is the best defense against it.",
                'cover_image' => 'images/blog/machame-vs-lemosho.jpg',
                'published_at' => Carbon::parse('2026-06-02'),
            ],
            [
                'title' => 'When Is the Best Time to See the Great Migration?',
                'slug' => 'best-time-to-see-the-great-migration',
                'excerpt' => 'The wildebeest migration moves in a rough clockwise loop through the Serengeti ecosystem all year. Here is what that means for your trip dates.',
                'body' => "There's no single 'migration season' — over a million wildebeest are on the move somewhere in the Serengeti ecosystem twelve months a year. What changes is where.\n\nJanuary to March, the herds gather on the short-grass plains of the southern Serengeti and Ndutu for calving season, when several thousand calves are born each day.\n\nJune brings the herds north and west, building toward the dramatic Grumeti and Mara River crossings that typically run from July through October.\n\nNovember and December see the migration turn south again ahead of the short rains, closing the loop.\n\nBecause the exact timing shifts year to year with rainfall, we don't build your itinerary around a fixed calendar date — we check where the herds actually are in the weeks before you travel and route your game drives accordingly.",
                'cover_image' => 'images/blog/great-migration.jpg',
                'published_at' => Carbon::parse('2026-05-14'),
            ],
            [
                'title' => 'A Realistic Packing List for Climbing Kilimanjaro',
                'slug' => 'realistic-packing-list-for-kilimanjaro',
                'excerpt' => 'You will pass through five climate zones on the way to the summit. Here is what actually earns a spot in your duffel bag.',
                'body' => "Kilimanjaro takes you from humid rainforest to arctic summit conditions in less than a week, so layering is everything.\n\nBase layers: two to three moisture-wicking base layers, top and bottom. Cotton is the enemy here — it holds moisture and chills you fast.\n\nInsulation: a fleece or softshell mid-layer, plus a down jacket for summit night, when temperatures regularly drop below -15°C.\n\nOuter shell: a waterproof, windproof jacket and pants. Rain in the forest zone is close to guaranteed on most routes.\n\nFootwear: broken-in waterproof hiking boots — never brand new — plus warm socks and gaiters for the scree sections near the summit.\n\nOther essentials: a headlamp with spare batteries for the midnight summit push, trekking poles, sunglasses rated for high UV, and a 3-liter hydration system.\n\nWe provide a full gear checklist with every booking, and can arrange rental equipment locally for anything you'd rather not fly with.",
                'cover_image' => 'images/blog/packing-list.jpg',
                'published_at' => Carbon::parse('2026-04-20'),
            ],
            [
                'title' => 'Zanzibar After Safari: Why the Beach Add-On Works So Well',
                'slug' => 'zanzibar-after-safari-why-it-works',
                'excerpt' => 'Dusty roads, early wake-ups, and long game drives are unforgettable — and exhausting. Here is why so many of our travelers end their trip on Zanzibar.',
                'body' => "Ask almost any experienced safari guide what to do after ten days of 5:30am wake-up calls and bumpy roads, and the answer is usually the same: go lie on a beach for a few days.\n\nZanzibar sits a short flight from both the northern safari circuit and Kilimanjaro, which makes it an easy add-on rather than a separate trip. The northeast coast in particular offers calm, shallow water even at low tide, ideal for swimming and snorkeling without much planning.\n\nBeyond the beach, a half-day in Stone Town's UNESCO-listed alleys and a spice farm tour add a dose of culture and history without eating into your relaxation time.\n\nMost of our clients book three to four nights on the island — enough to properly decompress without feeling like they're wasting safari days at home.",
                'cover_image' => 'images/blog/zanzibar-after-safari.jpg',
                'published_at' => Carbon::parse('2026-03-11'),
            ],
        ];

        foreach ($posts as $post) {
            Post::updateOrCreate(['slug' => $post['slug']], $post);
        }
    }
}
