<?php

namespace Database\Seeders;

use App\Models\Tour;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'tour_slug' => 'lemosho-route',
                'name' => 'Rachel Owens',
                'title' => 'Every one of us made the summit',
                'country' => 'United Kingdom',
                'rating' => 5,
                'quote' => 'Our guide read the mountain like a book — he adjusted our pace twice when altitude was clearly hitting people in the group, and every single one of us made the summit. Never felt like a number.',
            ],
            [
                'tour_slug' => 'northern-circuit-explorer',
                'name' => 'David Kim',
                'title' => 'Thirty meters from the river crossing',
                'country' => 'United States',
                'rating' => 5,
                'quote' => "They rerouted our whole third day when word came through that the migration had crossed the river north of where we were headed. We watched the crossing from thirty meters away. That kind of local knowledge isn't something you can book off a brochure.",
            ],
            [
                'tour_slug' => 'zanzibar-beach-escape',
                'name' => 'Amara Njoroge',
                'title' => 'The perfect end to our safari',
                'country' => 'South Africa',
                'rating' => 5,
                'quote' => 'Perfect end to our safari. Everything was arranged before we even landed on the island — transfers, hotel, even a sunset dhow cruise nobody asked us to upsell. It just showed up as a lovely surprise.',
            ],
            [
                'tour_slug' => 'machame-route',
                'name' => 'Thomas Berger',
                'title' => 'Honest about how hard it would be',
                'country' => 'Germany',
                'rating' => 5,
                'quote' => 'Honest from day one about how hard Machame would be and how to pace ourselves. No exaggerated promises, no sugar-coating — just an experienced crew who clearly cared whether we actually made it to Uhuru Peak.',
            ],
            [
                'tour_slug' => 'southern-circuit-wilderness',
                'name' => 'Priya Patel',
                'title' => 'Three other vehicles in five days',
                'country' => 'Canada',
                'rating' => 5,
                'quote' => "We saw three other vehicles in five days in Ruaha. Coming from a Serengeti trip years ago that felt more like a parking lot, this was the wilderness experience we didn't know we still wanted.",
            ],
            [
                'tour_slug' => null,
                'name' => 'Julien Mercier',
                'title' => 'One company, every handoff seamless',
                'country' => 'France',
                'rating' => 5,
                'quote' => 'Booked the whole trip — Kilimanjaro, safari, Zanzibar — through one company for the first time instead of stitching it together myself. Every handoff between guides and drivers was seamless.',
            ],
        ];

        foreach ($testimonials as $testimonial) {
            $tourId = $testimonial['tour_slug'] ? Tour::where('slug', $testimonial['tour_slug'])->value('id') : null;
            unset($testimonial['tour_slug']);
            $testimonial['tour_id'] = $tourId;
            $testimonial['is_approved'] = true;

            \App\Models\Testimonial::updateOrCreate(
                ['name' => $testimonial['name'], 'quote' => $testimonial['quote']],
                $testimonial
            );
        }
    }
}
