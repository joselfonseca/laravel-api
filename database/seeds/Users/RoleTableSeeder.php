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
        factory(\App\Models\Role::class)->create([
            'name' => 'Guest'
        ]);
        factory(\App\Models\Role::class)->create([
            'name' => 'Member'
        ]);
    }
}
