<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Empresa;
use App\Models\PedidoServico;
use App\Models\Contato;
use App\Models\Solicitante;
use Illuminate\Support\Facades\DB;

class PedidoServicoController extends Controller
{
    public function index()
    {
        // 1. Busca os itens (PedidoServico)
        $pedidoServicos = $this->getPedidoServicosDoUsuario();

        // 2. Busca os DADOS COMPLETOS das empresas correspondentes
        // Retorna um array onde a chave é "licitacao_id-servico_id" e o valor é o OBJETO com dados da empresa
        $mapaEmpresas = $this->getMapaEmpresasObjetos($pedidoServicos);

        // 3. Monta a lista unificada (ViewModel)
        // Transformamos a coleção original numa lista padronizada para a View
        $listaUnificada = $pedidoServicos->map(function ($item) use ($mapaEmpresas) {
            
            // Cria a chave única para buscar no mapa
            $chave = $item->pedido->licitacao_id . '-' . $item->servico_id;
            
            // Retorna um objeto simples contendo o par Item + Empresa
            return (object) [
                'pedido_servico' => $item,
                'empresa'        => $mapaEmpresas[$chave]
            ];
        });

        // 4. Manda a lista unificada para a view
        return view('pedidos.servicos-execucao', compact('listaUnificada'));
    }

   /**
     * Busca APENAS os itens (PedidoServico) baseada no papel do usuário.
     */
    private function getPedidoServicosDoUsuario()
    {
        $user = auth()->user();
        
        $query = PedidoServico::whereHas('pedido', function ($q) {
                $q->where('status', 'em_execucao');
            })
            ->with(['pedido', 'servico']); 

        // 1. Contato (Filtra apenas serviços da sua empresa)
        if (Contato::where('user_id', $user->id)->exists()) {
            $contato = Contato::where('user_id', $user->id)->first();
            $empresaId = $contato->empresa_id;

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

        // 3. Admin/Financeiro (Pega tudo)
        return $query->get();
    }

    /**
     * Recebe a lista de itens e retorna OBJETOS com dados da empresa.
     */
    private function getMapaEmpresasObjetos($pedidoServicos)
    {
        if ($pedidoServicos->isEmpty()) {
            return [];
        }

        $licitacaoIds = $pedidoServicos->pluck('pedido.licitacao_id')->unique();
        $servicoIds = $pedidoServicos->pluck('servico_id')->unique();

        return DB::table('empresa_licitacao_servico')
            ->join('empresas', 'empresa_licitacao_servico.empresa_id', '=', 'empresas.id')
            ->whereIn('licitacao_id', $licitacaoIds)
            ->whereIn('servico_id', $servicoIds)
            // Selecionamos as chaves para montar o mapa + TODOS os dados da empresa
            ->select(
                'empresa_licitacao_servico.licitacao_id', 
                'empresa_licitacao_servico.servico_id', 
                'empresas.*'
            )
            ->get()
            ->mapWithKeys(function ($item) {
                // A chave é composta, o valor é o objeto inteiro da linha (dados da empresa)
                return [$item->licitacao_id . '-' . $item->servico_id => $item];
            });
    }
}