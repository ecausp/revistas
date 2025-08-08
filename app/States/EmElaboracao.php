<?php

namespace App\States;

# se aprovado: envia/notifica a empresa para analise
class EmElaboracao extends StatusPedido
{
    public static string $name = 'em_elaboracao';
    public function label(): string
    {
        return 'Em Elaboração';
    }

    public function icon(): string
    {
        #icone de um sinal "+" em amarelo
        return 'fas fa-plus text-warning border border-warning rounded-pill p-2 mr-2 ml-5';
    }
}