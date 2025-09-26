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
        Schema::create('saldos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('revista_id')->constrained('revistas')->cascadeOnDelete();
            $table->foreignId('licitacao_id')->constrained('licitacoes')->cascadeOnDelete();
            $table->decimal('recurso_recebido', 15, 2)->default(0);
            $table->decimal('recurso_estagio', 15, 2)->default(0);
            $table->decimal('valor_contratado', 15, 2)->default(0);
            $table->timestamps();

            $table->unique(['revista_id', 'licitacao_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('saldos');
    }
};
