<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $pdfPath;

    /**
     * Crée une nouvelle instance du mailable.
     *
     * @param User $user
     * @param string|null $pdfPath
     */
    public function __construct(User $user, string $pdfPath = null)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    /**
     * Construit le message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Bienvenue à bord')
            ->view('emails.welcome')
            ->with([
                'user' => $this->user,
            ]);
            // ->attach($this->pdfPath);

        // if ($this->pdfPath) {
        //     $email->attach($this->pdfPath, [
        //         'as' => 'carte_fidelite.pdf',
        //         'mime' => 'application/pdf',
        //     ]);
        // }

        // return $email;
    }
}
