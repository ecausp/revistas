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

    public function color(): string
    {
        return 'yellow';
    }
}