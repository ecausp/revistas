<?php

namespace App\States;

# se aprovado: envia/notifica a empresa para execução
# se recusado: devolve para a Revista para edição do pedido (notifica a Empresa e a Revista). Nesta etapa o Financeiro faz o Empenho;
class EmAnaliseFinanceiro extends StatusPedido
{
    public static string $name = 'em_analise_financeiro';

    public function label(): string
    {
        return 'Em Análise (Financeiro)';
    }

    public function icon(): string
    {
        return 'fas fa-glasses text-info border border-info rounded-pill p-2';
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
