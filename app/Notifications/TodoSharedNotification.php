<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TodoSharedNotification extends Notification
{
    use Queueable;

    protected $name;
    protected $link;
    protected $sender;

    /**
     * Create a new notification instance.
     */
    public function __construct($name, $link, $sender)
    {
        $this->name = $name;
        $this->link = $link;
        $this->sender = $sender;
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
            ->greeting('Hi!')
            ->line('You have received a shared Todo item ' . $this->name . ' from ' . $this->sender->name)
            ->action('View Todo', url('/shared/private/' . $this->link))
            ->line('Thank you for using our application!');
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
