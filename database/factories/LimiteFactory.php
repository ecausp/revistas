<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Licitacao;
use App\Models\Revista;
use App\Models\Servico;
use App\Models\User;

class LimiteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'licitacao_id'   => Licitacao::factory(),
            'revista_id'     => Revista::factory(),
            'servico_id'     => Servico::factory(),
            'quantidade'     => $this->faker->numberBetween(1, 100),
            'valor_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'editor'         => User::factory(),
        ];
    }
}