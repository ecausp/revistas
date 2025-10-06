<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Revista;
use App\Models\Saldo;

use App\Models\Solicitante;
use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function create(Licitacao $licitacao)
    {
        $revistas = Revista::all();
        $saldosExistentes = Saldo::where('licitacao_id', $licitacao->id)->get();

        return view('saldos.create', compact('licitacao', 'revistas', 'saldosExistentes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'licitacao_id' => 'required|exists:licitacoes,id',
            'revista_id' => 'required|exists:revistas,id',
            'recurso_recebido' => 'required|numeric',
            'recurso_estagio' => 'required|numeric',
        ]);

        $saldo = Saldo::updateOrCreate(
            [
                'licitacao_id' => $request->licitacao_id,
                'revista_id' => $request->revista_id,
            ],
            [
                'recurso_recebido' => $request->recurso_recebido,
                'recurso_estagio' => $request->recurso_estagio,
            ]
        );

        return redirect()->back()
            ->with('success', 'Saldo salvo com sucesso!');
    }


    public function show(Saldo $saldo)
    {
        // se o usuario logado for solicitante da empresa, pode visualizar.
        // se não for, então redireciona.
        $id = auth()->user()->id;
        $solicitante = Solicitante::where('user_id', $id)->first();
        $revista = $saldo->revista;

        if(auth()->user()->hasRole('financeiro')|| $solicitante->revista_id == $revista->id)
        {
            $revista->load('pedidos.servicos');

            $servicos = $revista->pedidos->flatMap(function ($pedido) {
                return $pedido->servicos->map(function ($servico) use ($pedido) {
                    $servico->pedido_id = $pedido->id;
                    return $servico;
                });
            });

            return view('saldos.show', compact('saldo', 'servicos'));
    
        }
        else
        {
            session()->flash('error', 'Usuário não autorizado a acessar esse recurso');
        
            return redirect()->route('home');
        }
        
    }

    // função para que na criação de limite pege o valor recebido da revista para a licitação e valide 
    public function getValorRecebido(Request $request)
    {
        $licitacaoId = $request->query('licitacao_id');
        $revistaId = $request->query('revista_id');

        if (!$licitacaoId || !$revistaId) {
            return response()->json(['valor_recurso_financeiro' => 0]);
        }

        $saldo = Saldo::where('licitacao_id', $licitacaoId)
                      ->where('revista_id', $revistaId)
                      ->first();

        $valor = $saldo ? $saldo->recurso_recebido : 0;

        return response()->json(['valor_recurso_financeiro' => $valor]);
    }
}
