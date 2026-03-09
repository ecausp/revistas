<?php

namespace App\Http\Controllers;

class AjudaController extends Controller
{
    public function empresasAjuda()
    {
        return view('ajuda.empresas');
    }
    
    public function financeiroAjuda()
    {
        return view('ajuda.financeiro');
    }

    public function revistaAjuda()
    {
        return view('ajuda.revista');
    }

}