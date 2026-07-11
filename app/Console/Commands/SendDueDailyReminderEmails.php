<?php

namespace App\Console\Commands;

use App\Mail\DailyReminderDueMail;
use App\Models\DailyReminder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendDueDailyReminderEmails extends Command
{
    protected $signature = 'reminders:send-due-emails';

    protected $description = 'Email reminder owners 1 day, 12 hours, 3 hours and 1 hour before their daily reminder is due.';

    /**
     * Minutes before due => [tracking column, human-readable label].
     */
    private const STAGES = [
        1440 => ['reminder_1_day_email_sent_at', '1 day'],
        720 => ['reminder_12_hour_email_sent_at', '12 hours'],
        180 => ['reminder_3_hour_email_sent_at', '3 hours'],
        60 => ['reminder_1_hour_email_sent_at', '1 hour'],
    ];

    public function handle(): int
    {
        $now = now();
        $sent = 0;

        $reminders = DailyReminder::with(['user', 'creator'])
            ->where(function ($query) {
                foreach (self::STAGES as [$column, $label]) {
                    $query->orWhereNull($column);
                }
            })
            ->get();

        foreach ($reminders as $reminder) {
            if (!$reminder->user || !$reminder->user->email) {
                continue;
            }

            $dueAt = $reminder->dueAt();

            foreach (self::STAGES as $minutesBefore => [$column, $label]) {
                if ($reminder->{$column} !== null) {
                    continue;
                }

                $triggerAt = $dueAt->copy()->subMinutes($minutesBefore);

                if ($now->lt($triggerAt)) {
                    continue;
                }

                try {
                    Mail::to($reminder->user->email)->send(new DailyReminderDueMail($reminder, $label));
                    $reminder->forceFill([$column => $now])->save();
                    $sent++;
                } catch (Throwable $e) {
                    Log::error('Failed to send daily reminder email', [
                        'reminder_id' => $reminder->id,
                        'stage' => $label,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        $this->info("Sent {$sent} reminder email(s).");

        return self::SUCCESS;
    }
}
