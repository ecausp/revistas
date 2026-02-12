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
        // 1. Busca os itens
        $pedidoServicos = $this->getPedidoServicosDoUsuario();

        // 2. Busca o mapa de empresas correspondente a esses itens
        $mapaEmpresas = $this->getMapaEmpresas($pedidoServicos);

        // 3. Manda tudo pra view
        return view('pedidos.servicos-execucao', compact('pedidoServicos', 'mapaEmpresas'));
    }


   /**
     * Função 1: Busca APENAS os itens (PedidoServico) baseada no papel do usuário.
     * Retorna a coleção pura, sem inventar moda com nomes de empresa dentro.
     */
    private function getPedidoServicosDoUsuario()
    {
        $user = auth()->user();
        
        // Query base: Itens em execução com as relações necessárias
        $query = PedidoServico::whereHas('pedido', function ($q) {
                $q->where('status', 'em_execucao');
            })
            ->with(['pedido', 'servico']); // Trazemos o pedido (pra pegar licitacao_id) e servico

        // 1. Contato (Filtra apenas serviços da sua empresa)
        if (Contato::where('user_id', $user->id)->exists()) {
            $contato = Contato::where('user_id', $user->id)->first();
            $empresaId = $contato->empresa_id;

            // Faz o Join para garantir que é serviço desta empresa nesta licitação
            return $query->select('pedido_servico.*')
                ->join('pedidos', 'pedido_servico.pedido_id', '=', 'pedidos.id')
                ->join('empresa_licitacao_servico', function($join) use ($empresaId) {
                    $join->on('pedidos.licitacao_id', '=', 'empresa_licitacao_servico.licitacao_id')
                         ->on('pedido_servico.servico_id', '=', 'empresa_licitacao_servico.servico_id')
                         ->where('empresa_licitacao_servico.empresa_id', '=', $empresaId);
                })
                ->get();
        }

        // 2. Solicitante (Filtra pela revista)
        if (Solicitante::where('user_id', $user->id)->exists() && !$user->hasRole('admin') && !$user->hasRole('financeiro')) {
            $solicitante = Solicitante::where('user_id', $user->id)->first();
            $query->whereHas('pedido', function ($q) use ($solicitante) {
                $q->where('revista_id', $solicitante->revista_id);
            });
        }

        // 3. Admin/Financeiro (Pega tudo que sobrou da query base)
        return $query->get();
    }

    /**
     * Função 2: Recebe a lista de itens e descobre quem são as empresas.
     * Retorna um array onde a CHAVE é "licitacao_id-servico_id" e o VALOR é o "Nome da Empresa".
     */
    private function getMapaEmpresas($pedidoServicos)
    {
        if ($pedidoServicos->isEmpty()) {
            return [];
        }

        // Extrai IDs únicos para a consulta ser leve
        $licitacaoIds = $pedidoServicos->pluck('pedido.licitacao_id')->unique();
        $servicoIds = $pedidoServicos->pluck('servico_id')->unique();

        // Busca na tabela tripla
        return \DB::table('empresa_licitacao_servico')
            ->join('empresas', 'empresa_licitacao_servico.empresa_id', '=', 'empresas.id')
            ->whereIn('licitacao_id', $licitacaoIds)
            ->whereIn('servico_id', $servicoIds)
            ->select('licitacao_id', 'servico_id', 'empresas.razao_social')
            ->get()
            // Transforma num array associativo para busca rápida: ['5-10' => 'Empresa X']
            ->mapWithKeys(function ($item) {
                return [$item->licitacao_id . '-' . $item->servico_id => $item->razao_social];
            });
    }
}
