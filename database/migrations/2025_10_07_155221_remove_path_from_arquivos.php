<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Remove a coluna 'path' redundante, mantendo a coluna 'caminho' obrigatória.
     */
    public function up(): void
    {
        Schema::table('arquivos', function (Blueprint $table) {
            // Verifica se a coluna 'path' existe antes de tentar removê-la
            if (Schema::hasColumn('arquivos', 'path')) {
                $table->dropColumn('path');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Reverte a remoção da coluna 'path'.
     */
    public function down(): void
    {
        Schema::table('arquivos', function (Blueprint $table) {
            // Para o rollback, recriamos a coluna 'path' como nullable para evitar conflitos,
            // caso a coluna 'caminho' também seja NOT NULL.
            // Ajuste o tipo de coluna se ele for diferente de string.
            $table->string('path', 255)->nullable();
        });
    }
};
