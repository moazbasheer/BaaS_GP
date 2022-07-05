<?php

namespace App\Imports;

use App\Models\Passenger;
use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Spatie\Permission\Models\Role;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Collection;
use App\Models\Wallet;
use Hash;
use Auth;
use Validator;
class PassengersImport implements ToModel, WithHeadingRow
{
    use Importable;
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $validator = Validator::make($row, [
            'email' => ['required','email', 'unique:users'],
            'name' => ['required'],
            'password' => ['required'],
            'phone' => ['required'],
            'address' => ['required']
        ]);
        if($validator->fails()) {
            return;
        }
        $user = User::create([
            'email' => $row['email'],
            'password' => Hash::make($row['password']),
            'role_name' => 'passenger'
        ]);
        $role = Role::where('name', 'passenger')->first();
        $user->assignRole($role);
        $passenger = Passenger::create([
            'user_id' => User::latest()->first()->id,
            'organization_id' => Auth::user()->organization->id,
            'name' => $row['name'],
            'phone' => $row['phone'],
            'address' => $row['address']
        ]);
        return new Wallet([
            'passenger_id' => $passenger->id,
            'balance' => 0
        ]);
    }
    public function rules(): array
    {
        return [
        
            '*.email' => ['required','email', 'unique:users'],
            'name' => ['required'],
            'password' => ['required', 'unique:users'],
            'phone' => ['required'],
            'address' => ['required']
        ];
    }
}
