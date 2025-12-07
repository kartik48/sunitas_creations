<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CartItem>
 */
class CartItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }

    /**
     * Indicate that the cart item belongs to a user.
     */
    public function forUser($userId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId ?? \App\Models\User::factory(),
            'session_id' => null,
        ]);
    }

    /**
     * Indicate that the cart item belongs to a session.
     */
    public function forSession($sessionId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => $sessionId ?? fake()->uuid(),
        ]);
    }
}
