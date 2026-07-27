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
        Schema::create('chapter_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained('chapters')->cascadeOnDelete();
            $table->unsignedInteger('revision');
            $table->longText('content_markdown');
            $table->longText('content_html');
            $table->unsignedInteger('word_count');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->string('rollback_comment')->nullable();
            $table->timestamps();
            
            $table->unique(['chapter_id', 'revision']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapter_histories');
    }
};
