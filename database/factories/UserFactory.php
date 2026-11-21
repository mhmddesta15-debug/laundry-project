<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class UserFactory extends Factory
{
    /** Buat data pengguna contoh untuk pengujian dan seeding. */
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

    /** Ubah state factory agar menghasilkan pengguna tanpa verifikasi email. */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
