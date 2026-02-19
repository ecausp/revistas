<?php

namespace Database\Factories;

use App\Models\Chat;
use App\Models\User;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChatFactory extends Factory
{
    protected $model = Chat::class;

    public function definition()
    {
        return [
            'pedido_id' => Pedido::factory(),
            'user_id'   => User::factory(),
            'message'   => $this->faker->paragraph,
        ];
    }
}