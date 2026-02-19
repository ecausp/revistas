<?php

namespace Database\Factories;

use App\Models\Arquivo;
use App\Models\Pedido;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArquivoFactory extends Factory
{
    protected $model = Arquivo::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        // Gera um nome de arquivo fictício com extensões permitidas no seu sistema
        $extensoes = ['pdf', 'jpg', 'jpeg', 'png', 'tiff'];
        $nomeFake = $this->faker->word() . '.' . $this->faker->randomElement($extensoes);
        
        return [
            'pedido_id' => Pedido::factory(), // Cria um pedido automaticamente se não for passado
            'nome'      => now()->format('d-m-Y_H-i') . '_' . $nomeFake,
            'descricao' => $this->faker->sentence(4),
            'caminho'   => 'arquivos/' . $nomeFake,
        ];
    }
}