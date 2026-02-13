<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Revista;

class SolicitanteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // Cria um usuário novo
            'user_id' => User::factory(),
            // Cria uma revista nova
            'revista_id' => Revista::factory(),
        ];
    }
}