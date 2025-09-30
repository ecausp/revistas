<?php

namespace App\Listeners;


use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class ConcedeAdmin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        // Pega os codpes admins do .env
        $admins = array_filter(explode(',', env('SENHAUNICA_ADMINS', '')));

        // Se o codpes do usuário estiver na lista
        if (in_array($user->codpes, $admins)) {
            // Garante que ele tem a role "admin"
            if (!$user->hasRole('admin') || !$user->hasRole('financeiro')) {
                $user->assignRole('admin');
                $user->assignRole('financeiro');
            }
        }
    }
}
