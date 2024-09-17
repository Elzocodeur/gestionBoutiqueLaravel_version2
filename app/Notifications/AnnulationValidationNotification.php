<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AnnulationValidationNotification extends Notification
{
    use Queueable;

    protected $motif;

    /**
     * Create a new notification instance.
     *
     * @param string $motif
     */
    public function __construct($motif)
    {
        $this->motif = $motif;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail', 'sms'];
    }

    /**
     * Send the notification to the database.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Votre demande a été annulée pour la raison suivante : ' . $this->motif,
        ];
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
            ->line('Votre demande a été annulée.')
            ->line('Motif : ' . $this->motif)
            ->action('Voir les détails de la demande', url('/'))
            ->line('Nous vous remercions pour votre compréhension.');
    }

    /**
     * Send the notification via SMS.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toSms($notifiable)
    {
        return [
            'phoneNumber' => $notifiable->telephone,
            'message' => 'Votre demande a été annulée. Motif : ' . $this->motif,
        ];
    }
}
