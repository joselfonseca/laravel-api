<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\User::class, 1500)->create()->each(function($user) {
            $user->assignRole('Guest');
        });
        factory(\App\Models\User::class, 1500)->create()->each(function($user) {
            $user->assignRole('Member');
        });
    }
}
