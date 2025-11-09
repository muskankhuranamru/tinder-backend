<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');          // Who liked
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade'); // Who was liked
            $table->timestamps();
            
            $table->unique(['user_id', 'person_id']); // Prevent duplicate likes
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('likes');
    }
};
