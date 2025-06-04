<?php

namespace App\Http\Controllers;

use App\Models\Arquivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ArquivoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'pedido_id' => 'required|exists:pedidos,id',
            'descricao' => 'required|string|max:255',
            'arquivo' => 'required|file|mimes:pdf,jpg,jpeg,png,svg,tiff|max:3072', // 3MB
        ]);

        $file = $request->file('arquivo');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs('arquivos', $fileName, 'public');

        Arquivo::create([
            'pedido_id' => $request->pedido_id,
            'nome' => $fileName,
            'descricao' => $request->descricao,
        ]);

        return redirect()->back()->with('success', 'Arquivo adicionado com sucesso.');
    }

    public function destroy(Arquivo $arquivo)
    {
        Storage::disk('public')->delete('arquivos/' . $arquivo->nome);
        $arquivo->delete();
        return redirect()->back()->with('success', 'Arquivo excluído com sucesso.');
    }

}