<?php

namespace App\Mail;

use App\Models\DailyReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DailyReminderDueMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  string  $label  e.g. "1 day", "12 hours", "3 hours", "1 hour"
     */
    public function __construct(
        public DailyReminder $reminder,
        public string $label,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Reminder: due in {$this->label} - {$this->reminder->details}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.daily-reminder-due',
            with: [
                'reminder' => $this->reminder,
                'label' => $this->label,
            ],
        );
    }
}
