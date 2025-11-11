<?php

namespace App\States;

# se aprovado: envia/notifica a empresa do pagamento
class EmPagamento extends StatusPedido
{
    public static string $name = 'em_pagamento';
    public function label(): string
    {
        return 'Em Pagamento';
    }

    public function icon(): string
    {
        return 'fas fa-hand-holding-usd text-info border border-info rounded-pill p-2';
    }

    public function icon_style(): string
    {
        return 'font-size: 1rem; width: 32px; height: 32px';
    }

    public function color(): string
    {
        return 'info';
    }
}