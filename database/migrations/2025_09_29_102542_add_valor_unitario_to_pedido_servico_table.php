<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('pedido_servico', function (Blueprint $table) {
            $table->decimal('valor_unitario', 10, 2)->after('quantidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('pedido_servico', function (Blueprint $table) {
            $table->dropColumn('valor_unitario');
        });
    }
};
