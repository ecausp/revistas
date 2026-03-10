<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $guardName = 'web';
        $permissions = ['revistas.empresa', 'revistas.financeiro', 'revistas.revista'];
        $permissionModels = collect();

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($permissions as $permissionName) {
            $permissionModels->push(
                Permission::query()->firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => $guardName,
                ])
            );
        }

        $revista = Role::query()->firstOrCreate([
            'name' => 'revista',
            'guard_name' => $guardName,
        ]);
        $admin = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => $guardName,
        ]);

        $permissionModels = $permissionModels->unique('id')->values();

        $revista->syncPermissions($permissionModels);
        $admin->syncPermissions($permissionModels);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
