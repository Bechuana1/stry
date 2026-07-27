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
        Schema::create('chapter_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->unique()->constrained('chapters')->cascadeOnDelete();
            $table->unsignedBigInteger('total_views')->default(0);
            $table->unsignedBigInteger('unique_readers')->default(0);
            $table->timestamp('updated_at')->nullable();
            
            $table->index('total_views');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_stats');
    }
};
