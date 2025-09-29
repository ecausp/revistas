<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('pedido_historico')) {
            Schema::create('pedido_historico', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('id_pedido');
                $table->string('status_pedido');
                $table->unsignedBigInteger('id_pessoa');
                $table->text('observacao')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pedido_historico')) {
            Schema::dropIfExists('pedido_historico');
        }
    }
};

