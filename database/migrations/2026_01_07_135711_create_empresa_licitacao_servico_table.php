<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
    {
        Schema::create('empresa_licitacao_servico', function (Blueprint $table) {
            $table->id();

            $table->foreignId('empresa_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('licitacao_id')
                ->constrained('licitacoes')
                ->cascadeOnDelete();

            $table->foreignId('servico_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->integer('quantidade');
            $table->decimal('valor_unitario', 10, 2);
            $table->integer('prazo');

            $table->timestamps();

            $table->unique(
                ['empresa_id', 'licitacao_id', 'servico_id'],
                'uniq_emp_lic_serv'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_licitacao_servico');
    }

};
