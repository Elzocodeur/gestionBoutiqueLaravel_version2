<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $message;
    private $phoneNumber;

    /**
     * Create a new notification instance.
     *
     * @param string $message
     * @param string $phoneNumber
     */
    public function __construct(string $message, string $phoneNumber)
    {
        $this->message = $message;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'sms'];
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
     * Prepare the SMS message data.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toSms($notifiable)
    {
        return [
            'phoneNumber' => $this->phoneNumber,
            'message' => $this->message,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
}
