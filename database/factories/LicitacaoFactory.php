<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class LicitacaoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'descricao' => $this->faker->sentence(4),
            'ano' => $this->faker->year(),
            // Cria um usuário para ser o editor
            'editor' => User::factory(),
            'valor_total' => $this->faker->randomFloat(2, 500, 100000),
        ];
    }
}