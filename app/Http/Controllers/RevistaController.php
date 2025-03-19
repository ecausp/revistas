<?php

namespace App\Http\Controllers;

use App\Models\Revista;
use App\Models\User;
use Illuminate\Http\Request;
use Uspdev\Replicado\Financeiro;

class RevistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {   
        $this->authorize('admin');
        $revistas = Revista::all();
        return view('revistas.index', compact('revistas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('admin');
        $centros_despesas = Financeiro::ListarCentrosDespesas();
        return view('revistas.create', compact('centros_despesas'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('admin');

        $request->validate([
            'name' => 'required',
            'centro_despesa' => 'required',
        ]);

        $Revista = new Revista();
        $revista = [
            'editor' => $request->user()->id,
            "name" => $request->name,
            "centro_despesa" => $request->centro_despesa,
        ];
        Revista::create($revista);
        session()->flash('success', 'Cadastrado com sucesso!');
        return redirect()->route('revistas.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Revista $revista)
    {
        $this->authorize('admin');
        $editor = User::where('id', $revista->editor)->first();
        $updated_at = $revista->updated_at;
        return view('revistas.show', compact('revista', 'updated_at','editor' ) );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Revista $revista)
    {
        $this->authorize('admin');
        $centros_despesas = Financeiro::ListarCentrosDespesas();
        return view('revistas.edit', compact('revista', 'centros_despesas'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Revista $revista)
    {
        $this->authorize('admin');

        $request->validate([
            'name' => 'required',
            'centro_despesa' => 'required',
        ]);
        
        $revista_editada = [
            "editor" => $request->user()->id,
            "name" => $request->name,
            "centro_despesa" => $request->centro_despesa,  
        ];
        
        $revista->update($revista_editada);
        session()->flash('success', 'Atualizado com sucesso!');
        return redirect()->route('revistas.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Revista $revista)
    {
        $this->authorize('admin');
        $revista->delete();
        session()->flash('danger', 'Revista: ' . $revista->name . ' removida!');
        return redirect()->route('revistas.index');
    }
}