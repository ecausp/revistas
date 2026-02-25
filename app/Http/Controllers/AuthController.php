<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

use App\Mail\ResetPasswordMail;

use App\Models\User;

use Carbon\Carbon;

class AuthController extends Controller
{
    // carrega a página de login
    public function showLogin()
    {
        return view('auth.autenticacao_empresa');
    }

    // autentica o usuário e cria a sessão
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (auth()->attempt($credentials)) {
            return redirect()->route('home' );
        }

        session()->flash('danger', 'Credenciais inválidas!');
        return redirect()->route('login_empresa');
    }   

    // encerra a sessão
    public function logout(Request $request)
    {
        // Força o logout no guard 'web' explicitamente
        auth('web')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        session()->flash('success', 'Logout realizado com sucesso!');
        
        return redirect()->route('login_empresa');
    }

    // carrega a página de "esqueci a senha"
    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    // gera e envia o email para resetar a senha
    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user)
        {
            session()->flash('danger', 'Credenciais inválidas!');
            return back();
        }

        DB::table('password_resets')->where('email', $request->email)->delete();
        $token = Str::random(60);
        DB::table('password_resets')->insert([
            'email' => $user->email,
            'token' => $token,
            'created_at' => Carbon::now(),
        ]);

        $resetUrl = url("/reset-password/{$token}?email={$user->email}");

        try
        {
            Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));
            session()->flash('success', 'Se este e-mail existir, um link será enviado!');
        }
        catch (\Exception $e)
        {
            session()->flash('danger', 'O e-mail não pode ser enviado. Informe administrador do sistema.');
        }

        return back();
    }

    // carrega a página para resetar a senha
    public function showResetPassword($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // salva a nova senha no banco de dados
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|confirmed|min:6',
            'token' => 'required',
        ]);
        $record = DB::table('password_resets')
            ->where('email', $request->email)->first();
            if (!$record || $request->token !== $record->token) {
                session()->flash('danger', 'Token inválido!');
                return back();
        }
        
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            session()->flash('danger', 'Usuário não encontrado!');
            return back();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        
        DB::table('password_resets')->where('email', $request->email)->delete();
        session()->flash('success', 'Senha redefinida com sucesso!');
        return redirect()->route("home");

    }
}