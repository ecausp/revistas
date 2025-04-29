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
        Permission::firstOrCreate(['name' => 'revistas.empresa']);
        Permission::firstOrCreate(['name' => 'revistas.financeiro']);
        Permission::firstOrCreate(['name' => 'revistas.revista']);

        $revista = Role::firstOrCreate(['name' => 'revista']);
        $revista->givePermissionTo(['revistas.empresa', 'revistas.financeiro', 'revistas.revista']);

        $admin = Role::firstOrCreate(["name"=>"admin"]);
        $admin->givePermissionTo(['revistas.empresa', 'revistas.financeiro', 'revistas.revista']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};