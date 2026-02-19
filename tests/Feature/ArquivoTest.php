<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Pedido;
use App\Models\Arquivo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArquivoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Simula o disco 'public' para não salvar arquivos reais no seu ddev
        Storage::fake('public');
    }

    /** @test */
    public function um_arquivo_pode_ser_enviado_via_controller()
    {
        $user = User::factory()->create();
        $pedido = Pedido::factory()->create();
        
        // Simula um arquivo PDF de 100kb
        $arquivoFake = UploadedFile::fake()->create('documento.pdf', 100);

        $response = $this->actingAs($user)
            ->post(route('arquivos.store'), [
                'pedido_id' => $pedido->id,
                'descricao' => 'Projeto Inicial',
                'arquivo'   => $arquivoFake,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verifica no banco
        $this->assertDatabaseHas('arquivos', [
            'pedido_id' => $pedido->id,
            'descricao' => 'Projeto Inicial'
        ]);

        // Verifica se o arquivo físico foi salvo no "disco fake"
        $arquivoSalvo = Arquivo::first();
        Storage::disk('public')->assertExists($arquivoSalvo->caminho);
    }

    /** @test */
    public function model_deve_renomear_arquivo_removendo_espacos()
    {
        $pedido = Pedido::factory()->create();
        $arquivoComEspaco = UploadedFile::fake()->create('meu arquivo de teste.jpg', 50);

        $arquivo = Arquivo::salvarArquivo($arquivoComEspaco, 'Teste Nome', $pedido->id);

        // Verifica se o nome final não contém espaços (devido ao seu preg_replace no Model)
        $this->assertStringNotContainsString(' ', $arquivo->nome);
        Storage::disk('public')->assertExists($arquivo->caminho);
    }

    /** @test */
    public function exclusao_de_arquivo_deve_remover_registro_e_arquivo_fisico()
    {
        $pedido = Pedido::factory()->create();
        $file = UploadedFile::fake()->image('foto.png');
        
        // Cria via model para ter o arquivo físico no fake storage
        $arquivo = Arquivo::salvarArquivo($file, 'Foto para deletar', $pedido->id);
        $caminho = $arquivo->caminho;

        Storage::disk('public')->assertExists($caminho);

        // Executa a exclusão (método que você criou no Model)
        $arquivo->excluirArquivo();

        // Verifica se sumiu do banco e do disco
        $this->assertDatabaseMissing('arquivos', ['id' => $arquivo->id]);
        Storage::disk('public')->assertMissing($caminho);
    }

    /** @test */
    public function nao_deve_aceitar_arquivos_maiores_que_3mb()
    {
        $user = User::factory()->create();
        $pedido = Pedido::factory()->create();
        
        // 3073 KB é ligeiramente maior que o limite de 3072 definido no seu Controller
        $arquivoPesado = UploadedFile::fake()->create('pesado.pdf', 3073);

        $response = $this->actingAs($user)
            ->post(route('arquivos.store'), [
                'pedido_id' => $pedido->id,
                'descricao' => 'Arquivo Gigante',
                'arquivo'   => $arquivoPesado,
            ]);

        $response->assertSessionHasErrors('arquivo');
    }
}