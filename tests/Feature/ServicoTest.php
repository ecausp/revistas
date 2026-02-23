<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Servico;
use App\Models\Empresa;
use App\Models\Contato;
use App\Models\Licitacao;
use App\Models\EmpresaLicitacaoServico;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ServicoTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $financeiro;
    private $userComum;

    protected function setUp(): void
    {
        parent::setUp();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Mock da config necessária para o Controller
        config(['revistas.unidadesMedida' => 'laudas,páginas,A4,A5']);

        // Setup de Guards
        $guards = ['web', 'senhaunica'];
        foreach ($guards as $guard) {
            Permission::firstOrCreate(['name' => 'admin-financeiro', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);

            Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard])->givePermissionTo('admin');
            Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard])->givePermissionTo('financeiro');
        }

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->financeiro = User::factory()->create();
        $this->financeiro->assignRole('financeiro');

        $this->userComum = User::factory()->create();
    }

    /** @test */
    public function admin_acessa_index_e_ve_servicos_vinculados()
    {
        $servico = Servico::factory()->create();
        $empresa = Empresa::factory()->create();
        // CRIAR A LICITAÇÃO REAL AQUI:
        $licitacao = Licitacao::factory()->create(); 
        
        \DB::table('empresa_licitacao_servico')->insert([
            'empresa_id' => $empresa->id,
            'licitacao_id' => $licitacao->id, // USAR O ID REAL
            'servico_id' => $servico->id,
            'prazo' => 10,
            'valor_unitario' => 100.00
        ]);

        $response = $this->actingAs($this->admin)->get(route('servicos.index'));

        $response->assertOk();
    }

    /** @test */
    public function store_salva_novo_servico_com_editor()
    {
        $dados = [
            'descricao' => 'Serviço de Tradução',
            'unidade_medida' => 'hora',
            'valor_unitario' => '150,00' // O controller trata isso
        ];

        $response = $this->actingAs($this->financeiro)->post(route('servicos.store'), $dados);

        $response->assertRedirect(route('servicos.index'));
        $this->assertDatabaseHas('servicos', [
            'descricao' => 'Serviço de Tradução',
            'unidade_medida' => 'hora',
            'editor' => $this->financeiro->id
        ]);
    }

    /** @test */
    public function update_atualiza_servico_corretamente()
    {
        $servico = Servico::factory()->create();

        $dadosEditados = [
            'descricao' => 'Descricao Corrigida',
            'unidade_medida' => 'quilo'
        ];

        $response = $this->actingAs($this->admin)->put(route('servicos.update', $servico), $dadosEditados);

        $response->assertRedirect(route('servicos.index'));
        
        $this->assertDatabaseHas('servicos', [
            'id' => $servico->id,
            'descricao' => 'Descricao Corrigida'
        ]);
    }

    /** @test */
    public function contato_de_empresa_so_ve_seus_proprios_servicos_no_index()
    {
        $empresaA = Empresa::factory()->create();
        $licitacao = Licitacao::factory()->create();

        $userContato = User::factory()->create();
        Contato::create(['user_id' => $userContato->id, 'empresa_id' => $empresaA->id]);

        $servicoA = Servico::factory()->create();
        
        // Inserção na tabela pivô que o Controller consulta
        \DB::table('empresa_licitacao_servico')->insert([
            'empresa_id' => $empresaA->id,
            'licitacao_id' => $licitacao->id,
            'servico_id' => $servicoA->id,
            'valor_unitario' => 50,
            'prazo' => 5
        ]);

        $response = $this->actingAs($userContato)->get(route('servicos.index'));

        $response->assertOk();
        
        // Verificação: O Controller envia uma Collection para a view. 
        // Vamos garantir que o ID do serviço está lá.
        $servicosNaView = $response->viewData('servicos');
        $this->assertTrue($servicosNaView->contains('id', $servicoA->id));
    }

    /** @test */
    public function usuario_sem_empresa_e_sem_role_eh_bloqueado_no_index()
    {
        $response = $this->actingAs($this->userComum)->get(route('servicos.index'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('error', 'Usuário não está associado a nenhuma empresa!');
    }
}