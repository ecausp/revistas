<?php
// app/Mail/PedidoEnviadoMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoEnviadoParaEmpresaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;

    public function __construct($pedido)
    {
        $this->pedido = $pedido;
    }

    public function build()
    {
        return $this->subject("Pedido #{$this->pedido->id} - {$this->pedido->revista->name}")
                    ->view('emails.pedido_enviado_para_empresa_mail');
    }
}
