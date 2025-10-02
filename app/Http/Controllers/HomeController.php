<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitante;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $solicitante = Solicitante::where('user_id', $user->id)->first();
        // se o usuário que está logando é solicitante de uma revista, então redireciona ele pra visualizar a revista dele;
        if ($solicitante) {
            return redirect()->route('revistas.show', $solicitante->revista_id);
        }

        return view('welcome');
    }
}
