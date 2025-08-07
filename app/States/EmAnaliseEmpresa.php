<?php

namespace App\States;

# se aprovado: envia/notifica o financeiro para análise
# se recusado: devolve para a revista fazer a edição do pedido
class EmAnaliseEmpresa extends StatusServico
{
    public function label(): string
    {
        return 'Em Análise (Empresa)';
    }

    public function color(): string
    {
        return 'green';
    }
}
