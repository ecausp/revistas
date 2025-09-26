<?php

namespace App\Http\Controllers;

use App\Models\Licitacao;
use App\Models\Revista;

use Illuminate\Http\Request;

class SaldoController extends Controller
{
    public function create(Licitacao $licitacao)
    {
        return view('saldos.create', compact('licitacao'));
    }
}
