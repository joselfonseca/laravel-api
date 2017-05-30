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
        factory(\App\Entities\Role::class)->create([
            'name' => 'Guest'
        ]);
        factory(\App\Entities\Role::class)->create([
            'name' => 'Member'
        ]);
    }
}
