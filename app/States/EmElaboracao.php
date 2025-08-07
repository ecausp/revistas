<?php

namespace App\States;

# se aprovado: envia/notifica a empresa para analise
class EmElaboracao extends StatusPedido
{
    public static string $name = 'em_elaboracao';
    public function label(): string
    {
        return 'Em Elaboração (Revista)';
    }

    public function color(): string
    {
        return 'yellow';
    }
}