<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            // null = guess from the day text, 'none' = hide, 'custom' = use map_lat/map_lng, otherwise a key from config/places.php
            $table->string('map_place', 60)->nullable()->after('accommodation');
            $table->string('map_name')->nullable()->after('map_place');
            $table->decimal('map_lat', 9, 6)->nullable()->after('map_name');
            $table->decimal('map_lng', 9, 6)->nullable()->after('map_lat');
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropColumn(['map_place', 'map_name', 'map_lat', 'map_lng']);
        });
    }
};
