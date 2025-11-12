<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;

class ChatPolicy
{
    /**
     * Verifica se o usuário pode visualizar ou participar do chat desse pedido.
     */
    public function view(User $user, Pedido $pedido): bool
    {

        \Log::info('Policy chamada', [
            'user_id' => $user->id,
            'roles' => $user->roles->pluck('name'),
        ]);
        
        if ($user->hasRole('financeiro')) {
            return true;
        }

        // Usuário é solicitante da revista
        if ($pedido->revista->solicitantes->contains('user_id', $user->id)) {
            return true;
        }

        // Usuário é contato de alguma empresa dos serviços
        foreach ($pedido->servicos as $servico) {
            foreach ($servico->empresas as $empresa) {
                if ($empresa->contatos->contains('user_id', $user->id)) {
                    return true;
                }
            }
        }

        return false;
    }
}
