<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = ['admin'];

        foreach ($roles as $role_name){
            $role = \Spatie\Permission\Models\Role::create(['name' => $role_name]);
        }
    }
}
