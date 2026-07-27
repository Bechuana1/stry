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
        Schema::create('fan_art', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploader_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('character_id')->nullable()->constrained('characters')->cascadeOnDelete();
            $table->foreignId('story_id')->nullable()->constrained('stories')->cascadeOnDelete();
            $table->string('image_url');
            $table->string('image_public_id')->nullable();
            $table->string('title', 100)->nullable();
            $table->enum('moderation_status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('moderated_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamps();
            
            $table->index('moderation_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fan_art');
    }
};
