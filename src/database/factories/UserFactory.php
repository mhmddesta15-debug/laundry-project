<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name'     => fake()->name(),
            'phone'    => '08' . fake()->unique()->numerify('##########'),
            'email'    => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'address'  => fake()->address(),
            'role'     => 'pelanggan',
            'password' => Hash::make('password'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
