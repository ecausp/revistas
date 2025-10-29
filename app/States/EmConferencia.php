<?php

namespace App\States;

# se aprovado: envia/notifica o financeiro para pagamento
# se recusado: devolve para a Empresa para execuçao (notifica a Empresa)
class EmConferencia extends StatusPedido
{
    public static string $name = 'em_conferencia';
    public function label(): string
    {
        return 'Em Conferência';
    }

    public function icon(): string
    {
        return 'fas fa-user-check text-warning border border-warning rounded-pill p-2';
    }

    public function icon_style(): string
    {
        return 'font-size: 1rem; width: 32px; height: 32px';
    }

    public function color(): string
    {
        return 'warning';
    }
}