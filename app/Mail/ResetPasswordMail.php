<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;

    public function __construct($resetUrl)
    {
        $this->resetUrl = $resetUrl;
    }

    public function build()
    {
        return $this->from(
        config('mail.noreply.address'),
        config('mail.noreply.name'))
        ->subject('Redefinição de Senha')
        ->view('emails.reset-password')
        ->with(['resetUrl' => $this->resetUrl]);
    }
}