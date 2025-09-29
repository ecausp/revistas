<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contatos', function (Blueprint $table) {
            
            // 1. Adiciona a coluna com o tipo CORRETO (unsignedBigInteger)
            // O foreignId() garante que o tipo da coluna user_id seja igual ao 'id' em 'users'.
            // Você pode usar o after() se quiser especificar a posição da coluna.
            $table->foreignId('user_id')->nullable()->after('id'); // Assumindo que você quer ela depois do 'id' da tabela 'contatos'

            // 2. Adiciona a restrição de chave estrangeira (FK)
            // O constrained() cria automaticamente a restrição para a tabela 'users'.
            $table->foreign('user_id')
                  ->references('id')->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('contatos', function (Blueprint $table) {
            // Remove a restrição primeiro
            $table->dropForeign(['user_id']); 
            
            // Remove a coluna
            $table->dropColumn('user_id');
        });
    }
};