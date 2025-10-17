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
        /**
         * para ir acumulando o valor já cadastrado na licitação e validar os novos valores;
         */
        Schema::table('licitacoes', function (Blueprint $table) {
            $table->decimal('valor_cadastrado', 10, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('licitacoes', function (Blueprint $table) {
            $table->dropColumn('valor_cadastrado');
        });
    }
};
