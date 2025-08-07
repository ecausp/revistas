<?php

namespace App\States;

# se aprovado: envia/notifica a revista para conferencia
# nesta etapa o Chat entre Revista e Empresa auxilia nos ajustes necessários para execução do serviço;
class EmExecucao extends StatusPedido
{
    public static string $name = 'em_execucao';
    public function label(): string
    {
        return 'Em Execução (Empresa)';
    }

    public function color(): string
    {
        return 'green';
    }
}