<?php
// app/Mail/PedidoEnviadoMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PedidoPagoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $pedido;
    protected $iconePath;
    public function __construct($pedido)
    {
        $this->pedido = $pedido;
    }

    public function build()
    {
        return $this->from(
        'noreply@usp.br',
        config('mail.from.name'))->
        subject("Pedido #{$this->pedido->id} - {$this->pedido->revista->name} - Em Pagamento")
        ->view('emails.pedido_pago_mail');
    }
}
