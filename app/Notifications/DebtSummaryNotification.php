<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DebtSummaryNotification extends Notification
{
    use Queueable;

    protected $amountDue;

    public function __construct($amountDue)
    {
        $this->amountDue = $amountDue;
    }

    public function via($notifiable)
    {
        return ['database']; // Utiliser la base de données pour stocker la notification
    }

    public function toArray($notifiable)
    {
        return [
            'message' => "Montant total dû : $this->amountDue",
            'amount_due' => $this->amountDue,
        ];
    }
}
