<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->string('image')->nullable()->after('description');
            $table->string('image_caption')->nullable()->after('image');
            $table->text('note')->nullable()->after('image_caption');
            $table->json('activity')->nullable()->after('note');
            $table->json('accommodation')->nullable()->after('activity');
        });
    }

    public function down(): void
    {
        Schema::table('itinerary_items', function (Blueprint $table) {
            $table->dropColumn(['image', 'image_caption', 'note', 'activity', 'accommodation']);
        });
    }
};
