<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pedido;
use App\Models\Solicitante;
use App\Models\Revista;
use App\Models\Licitacao;
use App\States\EmElaboracao;

class PedidoFactory extends Factory
{
    protected $model = Pedido::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'solicitante_id' => Solicitante::factory(),
            'revista_id'     => Revista::factory(),
            'licitacao_id'   => Licitacao::factory(),
            'observacoes'    => $this->faker->sentence(),
            'status'         => EmElaboracao::class,
            'num_notificacao_fornecimento' => null,
        ];
    }
}