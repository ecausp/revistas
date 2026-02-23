<?php

namespace Database\Factories;

use App\Models\PedidoServico;
use App\Models\Pedido;
use App\Models\Servico;
use Illuminate\Database\Eloquent\Factories\Factory;

class PedidoServicoFactory extends Factory
{
    protected $model = PedidoServico::class;

    public function definition()
    {
        return [
            'pedido_id'      => Pedido::factory(),
            'servico_id'     => Servico::factory(),
            'quantidade'     => $this->faker->numberBetween(1, 10),
            'valor_unitario' => $this->faker->randomFloat(2, 10, 500),
            'num_empenho'    => null,
            'data_vencimento' => null,
        ];
    }

    /**
     * Estado para itens que já possuem empenho e data de vencimento (Em Execução).
     */
    public function emExecucao()
    {
        return $this->state(function (array $attributes) {
            return [
                'num_empenho'     => (string) $this->faker->numberBetween(100000, 999999),
                'data_vencimento' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            ];
        });
    }
}