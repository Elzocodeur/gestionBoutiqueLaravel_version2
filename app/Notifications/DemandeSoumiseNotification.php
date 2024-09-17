<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeSoumiseNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(private Client $client) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Store the notification data in the database.
     */
    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Une nouvelle demande a été soumise.',
            'sur_nom' => $this->client->surname,
            'telephone' => $this->client->telephone,
        ];
    }

    /**
     * Send the notification via email.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Une nouvelle demande a été soumise par le client : ' . $this->client->surname)
            ->line('Coordonnées du client :')
            ->line('Téléphone : ' . $this->client->telephone)
            ->line('Adresse : ' . $this->client->adresse)
            ->action('Voir la demande', url('/'))
            ->line('Merci d\'utiliser notre application!');
    }
}
