<?php
// app/Mail/PedidoDevolvidoParaRevistaMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoDevolvidoParaRevistaMail extends Mailable
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
                    ->view('emails.pedido_devolvido_para_revista_mail');
    }
}
