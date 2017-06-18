<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Entities\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'uuid' => $faker->uuid,
        'email' => $faker->unique()->safeEmail,
        'password' => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});

$factory->define(App\Entities\Role::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'uuid' => $faker->uuid
    ];
});

$factory->define(App\Entities\Permission::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->name,
        'uuid' => $faker->uuid
    ];
});