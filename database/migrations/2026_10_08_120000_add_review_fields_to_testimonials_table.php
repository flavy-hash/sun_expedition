<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->string('email')->nullable()->after('name');
            $table->string('title')->nullable()->after('rating');
            $table->json('images')->nullable()->after('quote');
            $table->boolean('is_approved')->default(false)->index()->after('images');
        });

        DB::table('testimonials')->update(['is_approved' => true]);
    }

    public function down(): void
    {
        Schema::table('testimonials', function (Blueprint $table) {
            $table->dropColumn(['email', 'title', 'images', 'is_approved']);
        });
    }
};
