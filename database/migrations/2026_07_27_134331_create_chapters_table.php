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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('story_id')->constrained('stories')->restrictOnDelete();
            $table->unsignedInteger('chapter_number');
            $table->string('slug', 200);
            $table->string('title', 200);
            $table->unsignedInteger('word_count')->default(0);
            $table->unsignedInteger('estimated_read_seconds')->default(0);
            $table->unsignedInteger('revision')->default(1);
            $table->boolean('is_latest_revision')->default(true);
            $table->enum('status', ['draft', 'scheduled', 'published', 'archived'])->default('draft');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('locked_until')->nullable();
            $table->unsignedInteger('gem_revenue_earned')->default(0);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['story_id', 'slug']);
            $table->unique(['story_id', 'chapter_number']);
            $table->index(['story_id', 'status', 'chapter_number']); // TOC Speed
            $table->index('locked_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
