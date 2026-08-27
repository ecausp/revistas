<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('revistas', function (Blueprint $table) {
            $table->string('centro_despesa')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('revistas')->whereNull('centro_despesa')->update(['centro_despesa' => '']);

        Schema::table('revistas', function (Blueprint $table) {
            $table->string('centro_despesa')->nullable(false)->change();
        });
    }
};
