<?php

namespace App\Http\Controllers;

use App\Models\EmpresaLicitacaoServico;
use Illuminate\Http\Request;

class LicitacaoServicoController extends Controller
{
    public function dadosRelacionados(Request $request)
    {
        $request->validate([
            'licitacao_id' => 'required|integer',
            'servico_id'   => 'required|integer',
        ]);

        $relacao = EmpresaLicitacaoServico::with('empresa')
            ->where('licitacao_id', $request->licitacao_id)
            ->where('servico_id', $request->servico_id)
            ->first();

        if (!$relacao) {
            return response()->json([
                'message' => 'Relação entre licitação e serviço não encontrada.'
            ], 404);
        }

        return response()->json([
            'valor_unitario' => $relacao->valor_unitario,
            'empresa' => [
                'id' => $relacao->empresa->id,
                'razao_social' => $relacao->empresa->razao_social,
            ],
        ]);
    }
}
