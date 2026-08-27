<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\EmpresaLicitacaoServico;
use App\Models\Licitacao;
use App\Models\Servico;
use App\Models\Empresa;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LicitacaoServicoTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        // Criamos um usuário para autenticar as requisições
        $this->user = User::factory()->create();
    }

    /** @test */
    public function dados_relacionados_retorna_json_com_valor_e_empresa_corretos()
    {
        $relacao = EmpresaLicitacaoServico::factory()->create([
            'valor_unitario' => 1234.56
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('licitacoes-servicos.dados', [
                'licitacao_id' => $relacao->licitacao_id,
                'servico_id'   => $relacao->servico_id,
            ]));

        $response->assertOk();
        
        $response->assertJson([
            'valor_unitario' => 1234.56,
            'empresa' => [
                'id' => $relacao->empresa->id,
                'razao_social' => $relacao->empresa->razao_social,
            ],
        ]);
    }

    /** @test */
    public function dados_relacionados_considera_apenas_a_empresa_vigente()
    {
        $licitacao = Licitacao::factory()->create();
        $servico = Servico::factory()->create();
        [$vencedora, $substituta] = Empresa::factory()->count(2)->create();

        $licitacao->empresas()->attach([
            $vencedora->id => [
                'classificacao' => 1,
                'vigente' => false,
                'motivo_substituicao' => null,
            ],
            $substituta->id => [
                'classificacao' => 2,
                'vigente' => true,
                'motivo_substituicao' => 'Substituição contratual.',
            ],
        ]);
        EmpresaLicitacaoServico::factory()->create([
            'empresa_id' => $vencedora->id,
            'licitacao_id' => $licitacao->id,
            'servico_id' => $servico->id,
            'valor_unitario' => 100,
        ]);
        EmpresaLicitacaoServico::factory()->create([
            'empresa_id' => $substituta->id,
            'licitacao_id' => $licitacao->id,
            'servico_id' => $servico->id,
            'valor_unitario' => 125,
        ]);

        $response = $this->actingAs($this->user)
            ->getJson(route('licitacoes-servicos.dados', [
                'licitacao_id' => $licitacao->id,
                'servico_id' => $servico->id,
            ]));

        $response->assertOk();
        $response->assertJson([
            'valor_unitario' => 125,
            'empresa' => ['id' => $substituta->id],
        ]);
    }

    /** @test */
    public function retorna_404_quando_a_relacao_licitacao_servico_nao_existe()
    {
        $licitacao = Licitacao::factory()->create();
        $servico = Servico::factory()->create();

        // Fazemos a busca sem que exista um registro na tabela empresa_licitacao_servico
        $response = $this->actingAs($this->user)
            ->getJson(route('licitacoes-servicos.dados', [
                'licitacao_id' => $licitacao->id,
                'servico_id'   => $servico->id,
            ]));

        $response->assertStatus(404);
        $response->assertJson([
            'message' => 'Relação entre licitação e serviço não encontrada.'
        ]);
    }

    /** @test */
    public function valida_obrigatoriedade_dos_parametros_licitacao_e_servico()
    {
        // Enviando request vazio para testar o $request->validate()
        $response = $this->actingAs($this->user)
            ->getJson(route('licitacoes-servicos.dados', []));

        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['licitacao_id', 'servico_id']);
    }

    /** @test */
    public function usuario_nao_autenticado_nao_acessa_dados_relacionados()
    {
        $response = $this->getJson(route('licitacoes-servicos.dados'));

        $response->assertUnauthorized();
    }
}
