<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class RevistaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'centro_despesa' => $this->faker->numberBetween(1000, 9999),
            // Cria um editor (User) automaticamente
            'editor' => User::factory(),
            // Simula uma lista de codpes (números USP) separados por vírgula
            'responsavel_centro_despesa' => implode(',', [
                $this->faker->unique()->numberBetween(100000, 999999),
                $this->faker->unique()->numberBetween(100000, 999999)
            ]),
            'email_revista' => $this->faker->unique()->safeEmail(),
        ];
    }
}