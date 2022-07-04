<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;
use Spatie\Permission\Models\Role;
use App\Models\Admin;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'role_name' => 'admin'
        ]);
        $user->assignRole(Role::where('name', 'admin')->get());
        Admin::create([
            'user_id' => $user->id,
            'username' => 'admin'
        ]);
    }
}
