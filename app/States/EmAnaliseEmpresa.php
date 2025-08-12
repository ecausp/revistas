<?php

namespace App\States;

# se aprovado: envia/notifica o financeiro para análise
# se recusado: devolve para a revista fazer a edição do pedido
class EmAnaliseEmpresa extends StatusPedido
{

    public static string $name = 'em_analise_empresa';
    public function label(): string
    {
        return 'Em Análise (Empresa)';
    }

    public function icon(): string
    {
        return 'fas fa-glasses text-success border border-success rounded-pill p-2 mr-2 ml-5';
    }

    public function icon_style(): string
    {
        return 'font-size: 1rem; width: 32px; height: 32px';
    }
}
