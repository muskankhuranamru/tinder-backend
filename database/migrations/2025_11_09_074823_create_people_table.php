<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create people table
 * 
 * Stores person profiles with their basic information and tracking fields
 * for like notifications.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('people', function (Blueprint $table) {
            $table->id();
            $table->string('name');                              // Person's name
            $table->integer('age');                              // Person's age
            $table->json('pictures');                            // Array of image URLs
            $table->string('location');                          // City, State format
            $table->integer('like_count')->default(0);          // Tracks likes for email threshold
            $table->boolean('admin_notified')->default(false);  // Prevents duplicate notifications
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('people');
    }
};
