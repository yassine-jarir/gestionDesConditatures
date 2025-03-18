<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public function build()
    {
        return $this->subject('Votre candidature a été reçue')
                    ->view('emails.application_confirmation');
    }
}
