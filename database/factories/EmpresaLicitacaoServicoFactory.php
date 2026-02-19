<?php

namespace Database\Factories;

use App\Models\EmpresaLicitacaoServico;
use App\Models\Empresa;
use App\Models\Licitacao;
use App\Models\Servico;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmpresaLicitacaoServicoFactory extends Factory
{
    protected $model = EmpresaLicitacaoServico::class;

    public function definition()
    {
        return [
            'empresa_id' => Empresa::factory(),
            'licitacao_id' => Licitacao::factory(),
            'servico_id' => Servico::factory(),
            'valor_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'prazo' => $this->faker->numberBetween(1, 60),
        ];
    }
}