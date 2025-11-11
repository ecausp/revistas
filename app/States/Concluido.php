<?php

namespace App\States;

class Concluido extends StatusPedido
{
    public static string $name = 'concluido';

    public function label(): string
    {
        return 'Concluído';
    }

    public function icon(): string
    {
        return 'fas fa-check-circle text-success border border-success rounded-pill p-2';
    }

    public function icon_style(): string
    {
        return 'font-size: 1rem; width: 32px; height: 32px';
    }

    public function color(): string
    {
        return 'success';
    }
}
