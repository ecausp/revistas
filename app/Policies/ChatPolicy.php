<?php

namespace App\Policies;

use App\Models\Pedido;
use App\Models\User;
use App\Models\Contato;
use App\Models\EmpresaLicitacaoServico;

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
        
        // 1. Financeiro tem acesso total
        if ($user->hasRole('financeiro')) {
            return true;
        }

        // 2. Usuário é solicitante da revista
        if ($pedido->revista->solicitantes->contains('user_id', $user->id)) {
            return true;
        }

        // 3. Usuário é contato de alguma empresa dos serviços (CORREÇÃO AQUI)
        if (Contato::where('user_id', $user->id)->exists()) {
            $contato = Contato::where('user_id', $user->id)->first();
            
            // Pega os IDs dos serviços do pedido
            $servicosDoPedido = $pedido->servicos->pluck('id');
            
            // Verifica se a empresa do contato presta algum desses serviços na licitação
            $empresaPrestaServico = EmpresaLicitacaoServico::where('licitacao_id', $pedido->licitacao_id)
                ->where('empresa_id', $contato->empresa_id)
                ->whereIn('servico_id', $servicosDoPedido)
                ->exists();
                
            if ($empresaPrestaServico) {
                return true;
            }
        }

        return false;
    }
}