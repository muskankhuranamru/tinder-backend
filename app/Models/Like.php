<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Like Model
 * 
 * Tracks when a user likes a person (swipe right action).
 * Unique constraint prevents duplicate likes.
 */
class Like extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'person_id'];

    public function person()
    {
        return $this->belongsTo(Person::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
