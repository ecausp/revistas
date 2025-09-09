<?php
// app/Mail/ContatoCadastradoMail.php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContatoCadastradoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $empresa;

    public function __construct($empresa)
    {
        $this->empresa = $empresa;
    }

    public function build()
    {
        return $this->subject("Contato cadastrado na Empresa #{$this->empresa->razao_social}")
                    ->view('emails.contato_cadastrado_na_empresa_mail');
    }
}
