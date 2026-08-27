<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_licitacao', function (Blueprint $table) {
            $table->unsignedInteger('classificacao')->nullable()->after('licitacao_id');
            $table->boolean('vigente')->default(false)->after('classificacao');
            $table->text('motivo_substituicao')->nullable()->after('vigente');
        });

        $licitacaoIds = DB::table('empresa_licitacao')->distinct()->pluck('licitacao_id');
        foreach ($licitacaoIds as $licitacaoId) {
            $vinculos = DB::table('empresa_licitacao')
                ->where('licitacao_id', $licitacaoId)
                ->orderBy('id')
                ->pluck('id');

            foreach ($vinculos as $indice => $vinculoId) {
                DB::table('empresa_licitacao')->where('id', $vinculoId)->update([
                    'classificacao' => $indice + 1,
                    'vigente' => $indice === 0,
                ]);
            }
        }

        Schema::table('empresa_licitacao', function (Blueprint $table) {
            $table->unique(['licitacao_id', 'classificacao']);
        });
    }

    public function down(): void
    {
        Schema::table('empresa_licitacao', function (Blueprint $table) {
            $table->dropUnique(['licitacao_id', 'classificacao']);
            $table->dropColumn(['classificacao', 'vigente', 'motivo_substituicao']);
        });
    }
};
