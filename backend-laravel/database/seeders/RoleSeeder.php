<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'organization']);
        Role::create(['name' => 'client']);
        Role::create(['name' => 'passenger']);
        Role::create(['name' => 'driver']);
        Role::create(['name' => 'admin']);
    }
}
