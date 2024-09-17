<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StockInsuffisantNotification extends Notification
{
    use Queueable;

    private $message;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     */
    public function __construct(string $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'sms', 'database'];
    }

    /**
     * Store the notification in the database.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }

    /**
     * Send the notification via SMS.
     *
     * @param mixed $notifiable
     * @return string
     */
    public function toSms($notifiable)
    {
        return $this->message;
    }

    /**
     * Send the notification via email.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line($this->message)
            ->action('Modifier votre demande', url('/'))
            ->line('Merci de votre compréhension.');
    }
}
