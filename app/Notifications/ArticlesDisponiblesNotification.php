<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticlesDisponiblesNotification extends Notification
{
    use Queueable;

    protected $articles;

    public function __construct($articles)
    {
        $this->articles = $articles;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Certains articles de votre demande sont disponibles : ' . implode(', ', $this->articles),
        ];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('Certains articles de votre demande ne sont pas disponibles.')
            ->line('Les articles disponibles sont : ' . implode(', ', $this->articles))
            ->action('Voir les articles disponibles', url('/'))
            ->line('Merci pour votre patience.');
    }
}
