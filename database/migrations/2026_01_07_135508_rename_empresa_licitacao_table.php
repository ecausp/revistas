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
        Schema::rename('empresa-licitacao', 'empresa_licitacao');
    }

    public function down(): void
    {
        Schema::rename('empresa_licitacao', 'empresa-licitacao');
    }

};
