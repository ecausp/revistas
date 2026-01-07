<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_licitacao', function (Blueprint $table) {
            if (Schema::hasColumn('empresa_licitacao', 'valor')) {
                $table->dropColumn('valor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresa_licitacao', function (Blueprint $table) {
            $table->decimal('valor', 10, 2)->default(0);
        });
    }
};
