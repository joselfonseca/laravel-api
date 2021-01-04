<?php

namespace Database\Seeders\Users;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     */
    public function run()
    {
        $user = \App\Models\User::factory()->create([
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com',
        ]);
        $user->assignRole('Administrator');
    }
}
