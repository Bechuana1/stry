<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('story_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->unique()->constrained('stories')->cascadeOnDelete();
            $table->unsignedBigInteger('total_views')->default(0);
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('story_stats');
    }
};
