<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\{User, Pedido, Solicitante, Revista, Licitacao, Servico, Empresa, Contato, EmpresaLicitacaoServico};
use App\States\{EmElaboracao, EmAnaliseEmpresa, EmAnaliseFinanceiro, EmExecucao, EmConferencia, EmPagamento, Concluido};
use App\Mail\{PedidoEnviadoParaEmpresaMail, PedidoEnviadoParaFinanceiroMail, PedidoEnviadoParaExecucaoMail, PedidoEmConferenciaMail, PedidoDevolvidoParaRevistaMail, PedidoEmPagamentoMail, PedidoConcluidoMail, PedidoDevolvidoParaExecucaoMail};
use Spatie\Permission\Models\{Role, Permission};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

class PedidoTest extends TestCase
{
    use RefreshDatabase;

    private $admin, $financeiro, $solicitanteUser, $solicitanteModel, $revista, $licitacao, $empresa, $contatoUser;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (['web', 'senhaunica'] as $guard) {
            Permission::firstOrCreate(['name' => 'admin', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard]);
            Permission::firstOrCreate(['name' => 'acesso-revista', 'guard_name' => $guard]);
            Role::firstOrCreate(['name' => 'admin', 'guard_name' => $guard])->givePermissionTo('admin');
            Role::firstOrCreate(['name' => 'financeiro', 'guard_name' => $guard])->givePermissionTo('financeiro');
        }

        $this->admin = User::factory()->create(); $this->admin->assignRole('admin');
        $this->financeiro = User::factory()->create(); $this->financeiro->assignRole('financeiro');
        
        $this->revista = Revista::factory()->create(['email_revista' => 'revista@teste.com']);
        $this->solicitanteUser = User::factory()->create();
        $this->solicitanteUser->givePermissionTo('acesso-revista');
        
        $this->solicitanteModel = Solicitante::create([
            'user_id' => $this->solicitanteUser->id, 
            'revista_id' => $this->revista->id
        ]);

        $this->empresa = Empresa::factory()->create();
        $this->contatoUser = User::factory()->create();
        Contato::create(['user_id' => $this->contatoUser->id, 'empresa_id' => $this->empresa->id]);

        $this->licitacao = Licitacao::factory()->create(['ano' => date('Y')]);
    }

    /** @test */
    public function valida_acesso_e_visibilidade()
    {
        $pedido = Pedido::factory()->create(['revista_id' => $this->revista->id, 'licitacao_id' => $this->licitacao->id]);
        $servico = Servico::factory()->create();
        $pedido->servicos()->attach($servico->id, ['quantidade' => 1, 'valor_unitario' => 10]);
        
        EmpresaLicitacaoServico::factory()->create([
            'empresa_id' => $this->empresa->id, 
            'servico_id' => $servico->id, 
            'licitacao_id' => $this->licitacao->id
        ]);

        $this->actingAs($this->solicitanteUser)->get(route('pedidos.index'))->assertSee($pedido->id);
        $this->actingAs($this->admin)->get(route('pedidos.index'))->assertSee($pedido->id);
    }

    /** @test */
    public function valida_fluxo_de_transicoes_e_notificacoes()
    {
        $servico = Servico::factory()->create();
        $pedido = Pedido::factory()->create([
            'status' => EmElaboracao::class, 
            'revista_id' => $this->revista->id, 
            'licitacao_id' => $this->licitacao->id
        ]);
        
        $pedido->servicos()->attach($servico->id, ['quantidade' => 1, 'valor_unitario' => 10]);
        
        EmpresaLicitacaoServico::factory()->create([
            'empresa_id' => $this->empresa->id, 
            'servico_id' => $servico->id, 
            'licitacao_id' => $this->licitacao->id
        ]);

        // 1. Elaboracao -> AnaliseEmpresa
        $this->actingAs($this->solicitanteUser)->post(route('pedidos.analise-empresa', $pedido));
        $this->assertInstanceOf(EmAnaliseEmpresa::class, $pedido->fresh()->status);
        Mail::assertSent(PedidoEnviadoParaEmpresaMail::class);

        // 2. AnaliseEmpresa -> AnaliseFinanceiro
        $this->actingAs($this->contatoUser)->post(route('pedidos.analise-financeiro', $pedido));
        $this->assertInstanceOf(EmAnaliseFinanceiro::class, $pedido->fresh()->status);

        // 3. AnaliseFinanceiro -> Execucao
        $pivot = $pedido->servicos->first()->pivot;
        $this->actingAs($this->financeiro)->post(route('pedidos.execucao', $pedido), [
            'empenhos' => [$pivot->id => 'EMP123'],
            'num_notificacao_fornecimento' => 'NF100'
        ]);
        $this->assertInstanceOf(EmExecucao::class, $pedido->fresh()->status);

        // 4. Execucao -> Conferencia
        // ATENÇÃO: Se o seu Controller usa $pedido->servicos()->first()->empresas(), 
        // o teste vai falhar se o relacionamento não existir no Model Servico.
        $this->actingAs($this->contatoUser)->post(route('pedidos.conferencia', $pedido));
        $this->assertInstanceOf(EmConferencia::class, $pedido->fresh()->status);
        Mail::assertSent(PedidoEmConferenciaMail::class);

        // 5. Conferencia -> Pagamento
        $this->actingAs($this->solicitanteUser)->post(route('pedidos.em-pagamento', $pedido));
        $this->assertInstanceOf(EmPagamento::class, $pedido->fresh()->status);

        // 6. Pagamento -> Concluido
        $this->actingAs($this->financeiro)->post(route('pedidos.concluido', $pedido));
        $this->assertInstanceOf(Concluido::class, $pedido->fresh()->status);
    }
}