<?php
// app/Mail/PedidoEnviadoParaExecucaoMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoEnviadoParaExecucaoMail extends Mailable
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
        subject("Pedido #{$this->pedido->id} - {$this->pedido->revista->name} - enviado para execução")
        ->view('emails.pedido_enviado_para_execucao_mail');
    }
}
