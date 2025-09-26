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
}
