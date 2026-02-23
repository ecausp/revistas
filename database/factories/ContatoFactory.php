<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Empresa;

class ContatoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            // Cria um usuário novo sempre que criar um contato
            'user_id' => User::factory(),
            // Cria uma empresa nova sempre que criar um contato
            'empresa_id' => Empresa::factory(),
        ];
    }
}