<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('title_highlight')->nullable();
            $table->text('intro')->nullable();
            $table->longText('body')->nullable();
            $table->json('stats')->nullable();
            $table->timestamps();
        });

        // The About page must exist for /about to render, so its starting content ships with the schema.
        DB::table('pages')->insert([
            'slug' => 'about',
            'title' => 'A Tanzanian company,',
            'title_highlight' => 'built by Tanzanians',
            'intro' => 'Suni Expedition was founded in Arusha with one idea: trips are better when the people planning them are the same people leading them on the ground.',
            'body' => implode("\n\n", [
                'Most travelers booking a Tanzania trip go through a chain of brokers before their money ever reaches the guides walking beside them on the mountain or driving them across the Serengeti. We cut that chain. Suni Expedition is locally owned and operated out of Arusha, and every safari, Kilimanjaro climb and Zanzibar escape we sell is designed and led in-house.',
                "That matters in ways that show up on the ground, not just on a website. It means our guides can reroute a game drive the morning word comes through that the migration has moved, without waiting on approval from an office overseas. It means the porters and cooks on your Kilimanjaro climb are paid above the standard wage, because we're the ones setting it. And it means when you email us a question, you're talking to someone who has actually stood at Uhuru Peak at sunrise or watched a river crossing from the group we were guiding that week.",
                "We keep our trip count deliberately manageable. We'd rather run fewer trips well than scale into the kind of impersonal, templated service we started this company to avoid.",
            ]),
            'stats' => json_encode([
                ['value' => '100%', 'label' => 'Locally owned and operated, no third-party brokers'],
                ['value' => '24h', 'label' => 'Typical response time on trip requests'],
                ['value' => '3', 'label' => 'Regions covered: Northern & Southern Circuit safaris, Kilimanjaro, Zanzibar'],
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
