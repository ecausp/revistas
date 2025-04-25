<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::firstOrCreate(['name' => 'licitacoes']);
        Permission::firstOrCreate(['name' => 'empresas']);
        Permission::firstOrCreate(['name' => 'servicos']);
        Permission::firstOrCreate(['name' => 'revistas']);
        Permission::firstOrCreate(['name' => 'limites']);


        $revista = Role::firstOrCreate(['name' => 'revista']);
        $revista->givePermissionTo(['revistas', 'servicos']);

        $financeiro = Role::firstOrCreate(["name"=>"financeiro"]);
        $financeiro->givePermissionTo(["licitacoes","empresas","servicos","revistas","limites"]);

        $empresa = Role::firstOrCreate(["name" => "empresa"]);
        $empresa->givePermissionTo(["empresas", "servicos"]);

        $admin = Role::firstOrCreate(["name"=>"admin"]);
        $admin->givePermissionTo(["licitacoes","empresas","servicos","revistas","limites"]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};