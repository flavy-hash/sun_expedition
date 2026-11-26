<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Kilimanjaro route key from config/kilimanjaro.php (machame, lemosho…); null for other packages.
            $table->string('route', 40)->nullable()->index()->after('circuit');
        });

        // Fill in existing Kilimanjaro packages from their names. Northern Circuit first: it also starts on Lemosho.
        $routes = ['northern circuit' => 'northern-circuit', 'machame' => 'machame', 'lemosho' => 'lemosho', 'rongai' => 'rongai', 'marangu' => 'marangu', 'umbwe' => 'umbwe'];

        foreach (DB::table('tours')->where('category', 'kilimanjaro')->whereNull('route')->get(['id', 'title', 'slug']) as $tour) {
            $text = strtolower($tour->title . ' ' . str_replace('-', ' ', $tour->slug));
            foreach ($routes as $needle => $route) {
                if (str_contains($text, $needle)) {
                    DB::table('tours')->where('id', $tour->id)->update(['route' => $route]);
                    break;
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn('route');
        });
    }
};
