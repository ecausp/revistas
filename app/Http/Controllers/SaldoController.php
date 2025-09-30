<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Revista;
use App\Models\Saldo;

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
        $revista = $saldo->revista;

        $revista->load('pedidos.servicos');

        $servicos = $revista->pedidos->flatMap(function ($pedido) {
            return $pedido->servicos->map(function ($servico) use ($pedido) {
                $servico->pedido_id = $pedido->id;
                return $servico;
            });
        });

        return view('saldos.show', compact('saldo', 'servicos'));
    }

}
