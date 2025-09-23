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

    public function icon(): string
    {
        #icone de um sinal "+" em amarelo
        return 'fas fa-tools text-success border border-success rounded-pill p-2';
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
