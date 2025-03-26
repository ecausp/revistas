<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use function Ramsey\Uuid\v1;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.autenticacao_empresa');
    }

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
        return redirect()->route('login');
    }   

    public function logout()
    {
        session()->forget('user_id'); // Remove o usuário da sessão

        session()->flash('sucess', 'Logout realizado com sucesso!');
        return redirect()->route('login');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
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

        Mail::to($user->email)->send(new ResetPasswordMail($resetUrl));


        session()->flash('success', 'Se este e-mail existir, um link será enviado!');
        return back();
    }

    public function showResetPassword($token, Request $request)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

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
            return back();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        
        DB::table('password_resets')->where('email', $request->email)->delete();
        session()->flash('success', 'Senha redefinida com sucesso!');
        return redirect()->route("home");

    }
}