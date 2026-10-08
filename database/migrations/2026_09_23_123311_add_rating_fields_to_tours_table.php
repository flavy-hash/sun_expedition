<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->decimal('rating', 2, 1)->nullable()->after('price_from');
            $table->unsignedInteger('reviews_count')->nullable()->after('rating');
            $table->string('highlight_stat')->nullable()->after('difficulty');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['rating', 'reviews_count', 'highlight_stat']);
        });
    }
};
