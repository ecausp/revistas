<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;

class ArquivoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'descricao' => 'required|string|max:255',
            'arquivo'   => 'required|file|mimes:pdf,jpg,jpeg,png,svg,tiff|max:3072', // 3MB
        ]);

        Arquivo::salvarArquivo(
            $request->file('arquivo'),
            $request->descricao,
            $request->pedido_id
        );

        return redirect()->back()->with('success', 'Arquivo adicionado com sucesso.');
    }

    public function destroy(Arquivo $arquivo)
    {
        $arquivo->excluirArquivo();

        return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
    }
    

}