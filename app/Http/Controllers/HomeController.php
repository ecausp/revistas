<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitante;
use App\Models\Contato;
use App\Models\Empresa;
use App\Models\PedidoServico;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Lógica do Solicitante (Redirecionamento)
        $solicitante = (($user) ? Solicitante::where('user_id', $user->id)->first() : null);
        
        if ($solicitante) {
            // Preserve flash messages when home performs an additional redirect.
            session()->reflash();
            return redirect()->route('revistas.show', $solicitante->revista_id);
        }
        
        // 2. Lógica do Contato (Visualizar serviços da empresa)
        $contato = (($user) ? Contato::where('user_id', $user->id)->first() : null);
        
        if ($contato) {
            $empresa = Empresa::find($contato->empresa_id);
    
            if ($empresa) {
                $pedidoServicos = PedidoServico::query()
                    ->select('pedido_servico.*')
                    ->join('pedidos', 'pedido_servico.pedido_id', '=', 'pedidos.id')
                    ->join('empresa_licitacao_servico', function($join) use ($empresa) {
                        $join->on('pedidos.licitacao_id', '=', 'empresa_licitacao_servico.licitacao_id')
                        ->on('pedido_servico.servico_id', '=', 'empresa_licitacao_servico.servico_id')
                        ->where('empresa_licitacao_servico.empresa_id', '=', $empresa->id);
                    })
                    ->where('pedidos.status', 'em_execucao')
                    ->with(['pedido', 'servico'])
                    ->get();
                
                $listaUnificada = $pedidoServicos->map(function ($item) use ($empresa) {
                    return (object) [
                        'pedido_servico' => $item,
                        'empresa'        => $empresa // Anexa a empresa logada em cada item
                    ];
                });

                return view('pedidos.servicos-execucao', compact('listaUnificada'));
            }
        }

        return view('welcome');
    }
}
