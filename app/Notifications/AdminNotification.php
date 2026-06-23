<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminNotification extends Notification
{
    use Queueable;
    private $title;
    private $message;
    private $type;
    private $icon;

    public function __construct( $title , $message , $type, $icon)
    {
        $this->title = $title;
        $this->message = $message;
        $this->type = $type;
        $this->icon = $icon;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'time' => now()->format('Y-m-d H:i:s'),
            'type' => $this->type,
            'icon' => $this->icon
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'time' => now()->format('Y-m-d H:i:s'),
            'type' => $this->type,
            'icon' => $this->icon
        ];
    }
}
