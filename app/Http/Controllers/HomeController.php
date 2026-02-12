<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Solicitante;
use App\Models\Contato;
use App\Models\Empresa;
use App\Models\EmpresaLicitacaoServico;
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

        $contato = (($user) ? Contato::where('user_id', $user->id)->first() : null);
        if ($contato) {
            $empresa = Empresa::where('id', $contato->empresa_id)->first();
    
            if ($empresa) {
                $pedidoServicos = PedidoServico::query()
                    ->select('pedido_servico.*')
                    ->join('pedidos', 'pedido_servico.pedido_id', '=', 'pedidos.id') // junta com a tabela de Pedidos para pegar a Licitação e o Status
                    ->join('empresa_licitacao_servico', function($join) use ($empresa) {
                        $join->on('pedidos.licitacao_id', '=', 'empresa_licitacao_servico.licitacao_id')
                        ->on('pedido_servico.servico_id', '=', 'empresa_licitacao_servico.servico_id')
                        ->where('empresa_licitacao_servico.empresa_id', '=', $empresa->id);
                    })
            
            ->where('pedidos.status', 'em_execucao')
            ->with(['pedido', 'servico'])
            ->get();
            
        return view('pedidos.servicos-execucao', compact('pedidoServicos', 'empresa'));
    }
}
return view('welcome');
    }
}
