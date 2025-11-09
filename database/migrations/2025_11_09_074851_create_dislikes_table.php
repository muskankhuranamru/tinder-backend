<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create dislikes table
 * 
 * Tracks when users dislike (swipe left) on people.
 * Foreign keys ensure referential integrity with cascade delete.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dislikes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');          // Who disliked
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade'); // Who was disliked
            $table->timestamps();
            
            $table->unique(['user_id', 'person_id']); // Prevent duplicate dislikes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dislikes');
    }
};
