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
        Schema::create('pedido_historico', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_pedido')
                ->constrained('pedidos')
                ->onDelete('cascade');

            $table->string('status_pedido');

            $table->foreignId('id_pessoa')
                ->constrained('users')
                ->restrictOnDelete();

            $table->text('observacao')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
