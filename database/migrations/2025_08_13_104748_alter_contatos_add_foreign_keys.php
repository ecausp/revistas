<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contatos', function (Blueprint $table) {
            // Garante que a coluna existe
            if (!Schema::hasColumn('contatos', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable();
            }
        });

        // Adiciona FK apenas se não existir
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'contatos'
              AND COLUMN_NAME = 'user_id'
        ");

        if (empty($foreignKeys)) {
            Schema::table('contatos', function (Blueprint $table) {
                $table->foreign('user_id')
                      ->references('id')->on('users')
                      ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('contatos', function (Blueprint $table) {
            if (Schema::hasColumn('contatos', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
        });
    }
};
