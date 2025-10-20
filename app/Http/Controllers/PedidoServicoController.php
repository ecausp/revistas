<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PedidoServico;
use App\Models\Contato;
use App\Models\Solicitante;

class PedidoServicoController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Inicia a query básica de PedidoServico
        $pedidoServicos = PedidoServico::query();

        // Filtra pelo status do pedido "em_execucao"
        $pedidoServicos->whereHas('pedido', function($query) {
            $query->where('status', 'em_execucao');
        });

        // Regra A: usuário financeiro vê tudo
        if (!$user->hasRole('financeiro')) {

            // Regra B: usuário com permissão 'revistas.empresa'
            if ($user->can('revistas.empresa')) {
                $contato = Contato::where('user_id', $user->id)->first();
                if ($contato && $contato->empresa_id) {
                    $empresaId = $contato->empresa_id;
                    $pedidoServicos->whereHas('servico.empresas', function($q) use ($empresaId) {
                        $q->where('id', $empresaId);
                    });
                } else {
                    // Se não tiver contato/empresa, retorna vazio
                    $pedidoServicos->whereRaw('0 = 1');
                }
            }
            // Regra C: usuário com permissão 'revistas.revista'
            elseif ($user->can('revistas.revista')) {
                $solicitante = Solicitante::where('user_id', $user->id)->first();
                if ($solicitante && $solicitante->revista_id) {
                    $revistaId = $solicitante->revista_id;
                    $pedidoServicos->whereHas('pedido', function($q) use ($revistaId) {
                        $q->where('revista_id', $revistaId);
                    });
                } else {
                    // Se não tiver solicitante/revista, retorna vazio
                    $pedidoServicos->whereRaw('0 = 1');
                }
            } 
            // Usuário sem nenhuma permissão relevante não vê nada
            else {
                $pedidoServicos->whereRaw('0 = 1');
            }
        }

        // Carrega os relacionamentos necessários
        $pedidoServicos = $pedidoServicos->with(['pedido', 'servico.empresas'])->get();

        return view('pedido-servicos.index', compact('pedidoServicos'));
    }
}
