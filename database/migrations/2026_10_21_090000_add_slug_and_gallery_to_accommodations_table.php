<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->json('gallery')->nullable()->after('image');
        });

        // Give existing stays a page address before making it unique.
        $taken = [];
        foreach (DB::table('accommodations')->orderBy('id')->get(['id', 'name']) as $stay) {
            $base = Str::slug($stay->name) ?: 'stay';
            $slug = $base;
            for ($i = 2; in_array($slug, $taken, true); $i++) {
                $slug = "{$base}-{$i}";
            }
            $taken[] = $slug;
            DB::table('accommodations')->where('id', $stay->id)->update(['slug' => $slug]);
        }

        Schema::table('accommodations', function (Blueprint $table) {
            $table->unique('slug');
        });
    }

    public function down(): void
    {
        Schema::table('accommodations', function (Blueprint $table) {
            $table->dropUnique(['slug']);
            $table->dropColumn(['slug', 'gallery']);
        });
    }
};
