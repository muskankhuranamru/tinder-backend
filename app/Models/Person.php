<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Person Model
 * 
 * Represents a person profile in the Tinder-like app with their basic info,
 * pictures, and tracking for likes received.
 */
class Person extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'age',
        'pictures',      // JSON array of image URLs
        'location',
        'like_count',    // Tracks total likes for email notification threshold
        'admin_notified' // Prevents duplicate email notifications
    ];

    protected $casts = [
        'pictures' => 'array',
        'age' => 'integer',
        'like_count' => 'integer',
        'admin_notified' => 'boolean'
    ];

    // Relationship: Person has many likes
    public function likes()
    {
        return $this->hasMany(Like::class, 'person_id');
    }

    // Relationship: Person has many dislikes
    public function dislikes()
    {
        return $this->hasMany(Dislike::class, 'person_id');
    }
}
