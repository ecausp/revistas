<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ServicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'descricao' => $this->faker->sentence(3),
            'unidade_medida' => $this->faker->randomElement(['laudas', 'páginas', 'A4', 'A5']),
            'editor' => User::factory(),
        ];
    }
}