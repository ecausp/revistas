<?php

namespace App\States;

# se aprovado: envia/notifica a empresa do pagamento
class Pago extends StatusServico
{
    public function label(): string
    {
        return 'Pago';
    }

    public function color(): string
    {
        return 'red';
    }
}