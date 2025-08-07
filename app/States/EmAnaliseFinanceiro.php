<?php

namespace App\States;

# se aprovado: envia/notifica a empresa para execução
# se recusado: devolve para a Revista para edição do pedido (notifica a Empresa e a Revista). Nesta etapa o Financeiro faz o Empenho;
class EmAnaliseFinanceiro extends StatusServico
{
    public function label(): string
    {
        return 'Em Análise (Financeiro)';
    }

    public function color(): string
    {
        return 'red';
    }
}
