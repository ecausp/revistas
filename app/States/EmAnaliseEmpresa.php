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
        $svg = <<<SVG
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="green" viewBox="0 0 576 512" style="vertical-align: middle;">
                <path d="M143.3 96c-14 0-26.5...Z"/>
                </svg>
                SVG;
        $encoded = base64_encode($svg);
        return '<img src="data:image/svg+xml;base64,' . $encoded . '" width="20" height="20" alt="status">';
    }

    public function icon_style(): string
    {
        return 'font-size: 1rem; width: 32px; height: 32px';
    }
}
