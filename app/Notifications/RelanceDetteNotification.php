<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RelanceDetteNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //I
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Vous avez une dette en attente de règlement.'
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Vous avez une dette en attente de règlement.')
            ->action('Voir les détails', url('/'))
            ->line('Merci pour utiliser notre application!');
    }
}
