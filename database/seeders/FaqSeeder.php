<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'category' => 'general',
                'question' => 'Do I need a visa to visit Tanzania?',
                'answer' => 'Most nationalities need a visa, which can be obtained online in advance or on arrival at Kilimanjaro International Airport. We send every client a visa checklist with their booking confirmation and can advise on your specific nationality.',
                'sort_order' => 1,
            ],
            [
                'category' => 'general',
                'question' => 'How far in advance should I book?',
                'answer' => 'We recommend booking 3-6 months ahead, especially for July-September and December-February travel, when park permits and camps fill up fastest. That said, we regularly put together trips on much shorter notice — just ask.',
                'sort_order' => 2,
            ],
            [
                'category' => 'general',
                'question' => 'Is Tanzania safe to travel to?',
                'answer' => 'Yes. Tanzania is politically stable and welcomes over a million visitors a year. As with any destination, normal travel precautions apply, and our guides and drivers are with you throughout every part of your trip.',
                'sort_order' => 3,
            ],
            [
                'category' => 'kilimanjaro',
                'question' => 'Which Kilimanjaro route has the best summit success rate?',
                'answer' => 'Longer routes with more acclimatization days succeed more often. Lemosho (8 days) consistently posts the highest success rates of our standard routes, followed by Machame with an extra acclimatization day added.',
                'sort_order' => 1,
            ],
            [
                'category' => 'kilimanjaro',
                'question' => 'Do I need previous climbing experience?',
                'answer' => 'No technical climbing skills are required on any of our standard routes — this is a trek, not a climb. Good general fitness and mental determination matter far more than prior mountaineering experience.',
                'sort_order' => 2,
            ],
            [
                'category' => 'kilimanjaro',
                'question' => 'What is included in the price of a Kilimanjaro climb?',
                'answer' => 'Park fees, camping or hut fees, a professional guide team, cooks and porters, all meals on the mountain, tents, and emergency oxygen are included. International flights, visas, tips, and personal gear are not.',
                'sort_order' => 3,
            ],
            [
                'category' => 'safari',
                'question' => 'When is the best time to see the wildebeest migration?',
                'answer' => "The migration moves year-round, but river crossings typically happen July-October in the northern Serengeti, and calving season runs January-March in the south. We build your itinerary around where the herds actually are that week.",
                'sort_order' => 1,
            ],
            [
                'category' => 'safari',
                'question' => 'How many people share a safari vehicle?',
                'answer' => 'We cap our vehicles well below the industry standard so every guest gets a window seat and unobstructed photos — never a packed minibus.',
                'sort_order' => 2,
            ],
            [
                'category' => 'zanzibar',
                'question' => 'Can Zanzibar be combined with a safari or Kilimanjaro climb?',
                'answer' => 'Yes — it\'s our most requested combination. Zanzibar is a short flight from the northern safari circuit or from Kilimanjaro, making it a natural place to unwind for a few days after dusty game drives or a mountain summit.',
                'sort_order' => 1,
            ],
            [
                'category' => 'zanzibar',
                'question' => 'What is the weather like in Zanzibar?',
                'answer' => 'Warm and tropical year-round, with the main rains falling April-May and shorter rains in November. December-March and June-October are generally the driest, sunniest windows for a beach stay.',
                'sort_order' => 2,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['category' => $faq['category'], 'question' => $faq['question']],
                $faq
            );
        }
    }
}
