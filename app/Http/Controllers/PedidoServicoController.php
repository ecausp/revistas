<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\PedidoServico;
use App\Models\Contato;
use App\Models\Solicitante;

class PedidoServicoController extends Controller
{
    public function index()
    {
        $pedidoServicos = $this->getPedidoServicosDoUsuario();

        return view('pedido-servico.index', compact('pedidoServicos'));
    }


    private function getPedidoServicosDoUsuario()
{
    $user = auth()->user();

    // 1. Admin ou Financeiro (vê todos)
    if ($user->hasRole('admin') || $user->hasRole('financeiro')) {
        return PedidoServico::whereHas('pedido', function ($q) {
            $q->where('status', 'em_execucao');
        })->with(['pedido', 'servico.empresas'])->get();
    }

    // 2. Contato (vê serviços da sua empresa)
    if (Contato::where('user_id', $user->id)->exists()) {
        $contato = Contato::where('user_id', $user->id)->first();
        $empresa = Empresa::where('id', $contato->empresa_id)->first();
        
        if ($empresa) {
            $servicosIds = $empresa->servicos->pluck('id');
            return PedidoServico::whereHas('pedido', function ($q) {
                    $q->where('status', 'em_execucao');
                })
                ->whereIn('servico_id', $servicosIds)
                ->with(['pedido', 'servico.empresas'])
                ->get();
        }
    }

    // 3. Solicitante (vê pedidos da sua revista)
    if (Solicitante::where('user_id', $user->id)->exists()) {
        $solicitante = Solicitante::where('user_id', $user->id)->first();
        return PedidoServico::whereHas('pedido', function ($q) use ($solicitante) {
                $q->where('status', 'em_execucao')
                  ->where('revista_id', $solicitante->revista_id);
            })
            ->with(['pedido', 'servico.empresas'])
            ->get();
    }

    // 4. Usuário sem papel relevante
    return collect();
}

}
