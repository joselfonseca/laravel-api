<?php

namespace Database\Factories;

use App\Models\SocialProvider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SocialProviderFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SocialProvider::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory()->create()->id,
            'provider' => 'github',
            'provider_id' => $this->faker->randomNumber(),
        ];
    }
}
