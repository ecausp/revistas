<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Revista;
use App\Models\Licitacao;

class SaldoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'revista_id' => Revista::factory(),
            'licitacao_id' => Licitacao::factory(),
            'recurso_recebido' => $this->faker->randomFloat(2, 1000, 5000),
            'recurso_estagio' => $this->faker->randomFloat(2, 0, 1000),
            'valor_contratado' => 0, // Geralmente começa em 0 até o método atualizaValorContratado ser chamado
        ];
    }
}