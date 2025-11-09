<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Mail\PopularPersonNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Scheduled Command: Check for Popular People
 * 
 * Runs hourly to check if any person has received more than 50 likes.
 * Sends email notification to admin and marks person as notified to prevent duplicates.
 */
class CheckPopularPeople extends Command
{
    protected $signature = 'people:check-popular';
    protected $description = 'Check for people with 50+ likes and notify admin';

    public function handle()
    {
        $adminEmail = env('ADMIN_EMAIL', 'admin@example.com');
        
        // Find people with 50+ likes who haven't been notified yet
        $popularPeople = Person::where('like_count', '>', 50)
            ->where('admin_notified', false)
            ->get();
        
        foreach ($popularPeople as $person) {
            // Send email notification
            Mail::to($adminEmail)->send(new PopularPersonNotification($person));
            
            // Mark as notified to prevent duplicate emails
            $person->update(['admin_notified' => true]);
            
            $this->info("✓ Notification sent for {$person->name} ({$person->like_count} likes)");
        }
        
        $this->info("Checked {$popularPeople->count()} popular " . str('person')->plural($popularPeople->count()));
        
        return 0;
    }
}
