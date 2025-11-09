<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Dislike Model
 * 
 * Tracks when a user dislikes a person (swipe left action).
 * Unique constraint prevents duplicate dislikes.
 */
class Dislike extends Model
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
