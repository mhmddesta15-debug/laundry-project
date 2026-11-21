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
            'address'  => fake()->address(),
            'role'     => 'pelanggan',
            'password' => Hash::make('password'),
        ];
    }
}
