<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeValideeNotification extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['database', 'mail', 'sms'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Votre demande a été validée. Vous pouvez maintenant récupérer vos produits en boutique.',
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Votre demande a été validée.')
            ->line('Vous pouvez maintenant passer en boutique pour récupérer vos produits.')
            ->action('Voir les détails de la demande', url('/'))
            ->line('Merci d\'avoir choisi notre service.');
    }

    public function toSms($notifiable)
    {
        return 'Votre demande a été validée. Vous pouvez maintenant récupérer vos produits en boutique.';
    }
}
