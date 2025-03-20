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
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('editor');
        });
        Schema::table('servicos', function (Blueprint $table) {
            $table->integer('editor');
        });
        Schema::table('revistas', function (Blueprint $table) {
            $table->integer('editor');
        });
        Schema::table('licitacoes', function (Blueprint $table) {
            $table->integer('editor')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            //
        });
    }
};