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

        $solicitante = (($user) ? Solicitante::where('user_id', $user->id)->first() : null);
        // se o usuário que está logando é solicitante de uma revista, então redireciona ele pra visualizar a revista dele;
        if ($solicitante) {
            return redirect()->route('revistas.show', $solicitante->revista_id);
        }
        
        // se o usuário que está logando é contato de uma empresa, então redireciona ele para visualizar a lista de serviços de pedidos em execução;
        if (Contato::where('user_id', $user->id)->exists()) {
            $contato = Contato::where('user_id', $user->id)->first();
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
        
            if ($empresa) {
                $servicosIds = $empresa->servicos->pluck('id');
                $pedidoServicos =  PedidoServico::whereHas('pedido', function ($q) {
                        $q->where('status', 'em_execucao');
                    })
                    ->whereIn('servico_id', $servicosIds)
                    ->with(['pedido', 'servico.empresas'])
                    ->get();
            }
            return view('pedido-servico.index', compact('pedidoServicos'));
        }
        return view('welcome');
    }
}
