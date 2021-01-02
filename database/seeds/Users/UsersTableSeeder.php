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
        $user = \App\Models\User::factory()->create([
            'name' => 'Jose Fonseca',
            'email' => 'jose@example.com'
        ]);
        $user->assignRole('Administrator');
    }
}
