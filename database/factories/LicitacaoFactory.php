<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Licitacoes;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Licitacao>
 */
class LicitacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
            'descricao' => $this->faker->sentence,
            'ano' => $this->faker->year,
            'valor_total' => $this->faker->randomFloat(2, 0, 1000000),
            'editor' => $this->faker->name(),
        ];
    }
}