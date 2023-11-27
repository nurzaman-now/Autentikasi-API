<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class EmailMessage extends Notification
{
    use Queueable;
    private $title;
    private $description;
    private $content;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $description, $content)
    {
        $this->title = $title;
        $this->description = $description;
        $this->content = $content;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(Lang::get($this->title))
            ->line(Lang::get('Pemberitahuan ' . $this->title . ' untuk anda Anda.'))
            ->line(Lang::get($this->content))
            ->line(Lang::get($this->description));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
