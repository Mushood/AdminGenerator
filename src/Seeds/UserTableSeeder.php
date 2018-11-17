<?php

use Illuminate\Database\Seeder;
use Laravel\Passport\Client;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = new \App\Models\User();
        $user->name = "admin";
        $user->email = "admin@test.com";
        $user->password = \Illuminate\Support\Facades\Hash::make('secret');
        $user->save();
        $user->assignRole('admin');

        $token = $user->createToken('access', ['*'])->accessToken;
        $user->api_token = $token;
        $user->save();
    }
}
