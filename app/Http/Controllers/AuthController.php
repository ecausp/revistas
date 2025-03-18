<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            //'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        //if (!$user || !Hash::check($request->password, $user->password)) {
        //    return redirect()->route('login')->with('error', 'Credenciais inválidas');
        //}

        session(['user_id' => $user->id]); // Salva o usuário na sessão

        return redirect()->route('dashboard');
    }

    public function dashboard()
    {
        $userId = session('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Faça login primeiro.');
        }

        $user = User::find($userId);

        return view('auth.dashboard', compact('user'));
    }

    public function logout()
    {
        session()->forget('user_id'); // Remove o usuário da sessão

        return redirect()->route('login')->with('message', 'Logout realizado com sucesso');
    }
}