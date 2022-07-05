<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Wallet;
use App\Models\User;
class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::get();
        foreach($users as $user) {
            if($user->role_name == 'organization') {
                if(!$user->organization) {
                    continue;
                }
                Wallet::create([
                    'organization_id' => $user->organization->id,
                    'balance' => 0
                ]);
            } elseif($user->role_name == 'client') {
                if(!$user->client) {
                    continue;
                }
                Wallet::create([
                    'client_id' => $user->client->id,
                    'balance' => 0
                ]);
            } elseif($user->role_name == 'passenger') {
                if(!$user->passenger) {
                    continue;
                }
                Wallet::create([
                    'passenger_id' => $user->passenger->id,
                    'balance' => 0
                ]);
            }
        }
    }
}
