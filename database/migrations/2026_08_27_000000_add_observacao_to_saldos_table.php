<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('saldos', function (Blueprint $table) {
            $table->text('observacao')->nullable()->after('recurso_estagio');
        });
    }

    public function down(): void
    {
        Schema::table('saldos', function (Blueprint $table) {
            $table->dropColumn('observacao');
        });
    }
};
