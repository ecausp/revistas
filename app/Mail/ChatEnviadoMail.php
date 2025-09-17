<?php
// app/Mail/ContatoCadastradoMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChatEnviadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;

    public function __construct($pedido)
    {
        $this->pedido = $pedido;
    }

    public function build()
    {
        return $this->from(
        'noreply@usp.br',
        config('mail.from.name'))->
        subject("Nova mensagem no chat do pedido - #{$this->pedido->id}")
        ->view('emails.chat_enviado_mail');
    }
}
