<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Shown instead of "N days" for trips shorter than a day, e.g. "5–6".
            $table->string('duration_hours', 20)->nullable()->after('duration_days');
            // Day trips: key from config/day_trips.php that groups the trip in the menu (arusha, moshi…).
            $table->string('departs_from', 40)->nullable()->index()->after('route');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropIndex(['departs_from']);
            $table->dropColumn(['duration_hours', 'departs_from']);
        });
    }
};
