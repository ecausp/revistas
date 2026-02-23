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
        Schema::table('empresa_licitacao_servico', function (Blueprint $table) {
            if (Schema::hasColumn('empresa_licitacao_servico', 'quantidade')) {
                $table->dropColumn('quantidade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresa_licitacao_servico', function (Blueprint $table) {
            //
        });
    }
};
