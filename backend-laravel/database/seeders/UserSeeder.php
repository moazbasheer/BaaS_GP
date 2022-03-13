<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Admin',
            'email' => "admin@admin.com",
            'password' => Hash::make('123456'),
            'role_name' => 'admin'
        ]);
        User::create([
            'name' => 'Moaz Basheer',
            'email' => "moaz@gmail.com",
            'password' => Hash::make('123456'),
            'role_name' => 'organization'
        ]);
        User::create([
            'name' => 'Fady Emad',
            'email' => "fady@gmail.com",
            'password' => Hash::make('123456'),
            'role_name' => 'organization'
        ]);
        User::create([
            'name' => 'Mohamed Mohsen',
            'email' => "mohamed@gmail.com",
            'password' => Hash::make('123456'),
            'role_name' => 'organization'
        ]);
        User::create([
            'name' => 'Mahmoud Ashraf',
            'email' => "mahmoud@gmail.com",
            'password' => Hash::make('123456'),
            'role_name' => 'organization'
        ]);
    }
}
