<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
        {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            Permission::create(['name' => 'manage_shops']);
            Permission::create(['name' => 'manage_cashiers']);
            Permission::create(['name' => 'crud_products']);
            Permission::create(['name' => 'create_sales']);
            Permission::create(['name' => 'view_sales']);
            Permission::create(['name' => 'edit_own_profile']);

            $super = Role::create(['name' => 'super-admin']);
            $super->givePermissionTo(Permission::all());

            $admin = Role::create(['name' => 'admin']);
            $admin->givePermissionTo(['manage_cashiers', 'crud_products', 'view_sales', 'edit_own_profile']);

            $kasir = Role::create(['name' => 'kasir']);
            $kasir->givePermissionTo(['create_sales', 'view_sales', 'edit_own_profile']);
        }
}
