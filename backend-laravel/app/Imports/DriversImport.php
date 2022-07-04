<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Validator;
use Hash;
use App\Http\Controllers\UserController;
use App\Models\User;
use App\Models\Driver;
use Spatie\Permission\Models\Role;

class DriversImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $validator = Validator::make($row, [
            'email' => ['required','email', 'unique:users'],
            'name' => ['required', 'string'],
            'password' => ['required'],
            'phone' => ['required', 'size:10'],
            'address' => ['required', 'string']
        ]);
        
        if($validator->fails()) {
            $message = [];
            $message = UserController::format_message($message, $validator);
            return response([
                'status' => false,
                'message' => $message
            ], 200);
        }
        $user = User::create([
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'role_name' => 'driver'
        ]);
        $role = Role::where('name', 'driver')->first();
        $user->assignRole($role);
        return new Driver([
            'user_id' => $user->id,
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address'],
            'activated' => 1
        ]);
        
    }
}
