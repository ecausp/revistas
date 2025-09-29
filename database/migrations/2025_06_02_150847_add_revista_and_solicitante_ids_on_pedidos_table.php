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
        Schema::table('pedidos', function (Blueprint $table) {
            if (!Schema::hasColumn('pedidos', 'solicitante_id')) {
                $table->unsignedBigInteger('solicitante_id')->nullable(false);
            }

            if (!Schema::hasColumn('pedidos', 'revista_id')) {
                $table->unsignedBigInteger('revista_id')->nullable(false);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            if (Schema::hasColumn('pedidos', 'solicitante_id')) {
                $table->dropColumn('solicitante_id');
            }

            if (Schema::hasColumn('pedidos', 'revista_id')) {
                $table->dropColumn('revista_id');
            }
        });
    }
};