<?php

namespace App\Mail;

use App\Models\Person;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Email notification sent to admin when a person receives 50+ likes
 */
class PopularPersonNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Person $person)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Popular Person Alert - ' . $this->person->name,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.popular-person');
    }

    public function attachments(): array
    {
        return [];
    }
}
