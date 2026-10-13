<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Deliberately no IP address, user agent or cookie id: see App\Http\Middleware\LogPageView.
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->char('visitor_hash', 16);
            $table->string('path', 500);
            $table->string('page_type', 50);
            $table->foreignId('tour_id')->nullable()->constrained()->nullOnDelete();
            $table->string('device', 10);
            $table->string('referrer_host')->nullable();
            $table->char('country', 2)->nullable();
            $table->timestamp('created_at')->index();

            $table->index(['tour_id', 'created_at']);
            $table->index(['country', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
