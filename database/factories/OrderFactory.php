<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_number' => 'ORD-' . strtoupper(fake()->unique()->bothify('??########')),
            'total_amount' => fake()->randomFloat(2, 100, 10000),
            'status' => fake()->randomElement(['pending', 'processing', 'completed', 'cancelled']),
            'payment_method' => fake()->randomElement(['cod', 'stripe']),
            'payment_status' => fake()->randomElement(['pending', 'paid', 'failed', 'refunded']),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => fake()->phoneNumber(),
            'shipping_address' => fake()->address(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
