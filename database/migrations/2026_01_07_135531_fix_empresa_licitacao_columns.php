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
        Schema::table('empresa_licitacao', function (Blueprint $table) {
            // remove colunas antigas
            $table->dropColumn(['empresa_id', 'licitacao_id']);
        });

        Schema::table('empresa_licitacao', function (Blueprint $table) {
            // recria corretamente
            $table->foreignId('empresa_id')->constrained('empresas')->cascadeOnDelete();

            $table->foreignId('licitacao_id')->constrained('licitacoes')->cascadeOnDelete();

            $table->unique(['empresa_id', 'licitacao_id']);
        });
    }

    public function down(): void
    {
        //
    }

};
