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
       Schema::create('stories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->constrained('users')->restrictOnDelete();
            $table->string('slug', 200)->unique();
            $table->string('title', 200);
            $table->text('synopsis');
            $table->string('genre', 50);
            $table->json('tags')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('cover_alt_text')->nullable();
            $table->unsignedInteger('chapters_count')->default(0);
            $table->unsignedInteger('followers_count')->default(0);
            $table->enum('status', ['ongoing', 'completed', 'hiatus'])->default('ongoing');
            $table->timestamp('last_chapter_published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['genre', 'status']);
            $table->fullText(['title', 'synopsis']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stories');
    }
};
