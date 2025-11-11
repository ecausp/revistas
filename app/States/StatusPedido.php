<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class StatusPedido extends State
{
    abstract public function label(): string;

    public static function config(): StateConfig
    {
        return parent::config()
            ->default(EmElaboracao::class)
            ->allowTransition(EmElaboracao::class, EmAnaliseEmpresa::class)
            ->allowTransition(EmAnaliseEmpresa::class, EmAnaliseFinanceiro::class)
            ->allowTransition(EmAnaliseEmpresa::class, EmElaboracao::class)
            ->allowTransition(EmAnaliseFinanceiro::class, EmExecucao::class)
            ->allowTransition(EmAnaliseFinanceiro::class, EmElaboracao::class)
            ->allowTransition(EmExecucao::class, EmConferencia::class)
            ->allowTransition(EmConferencia::class, EmPagamento::class)
            ->allowTransition(EmConferencia::class, EmExecucao::class)
            ->allowTransition(EmPagamento::class, Concluido::class);
    }
}