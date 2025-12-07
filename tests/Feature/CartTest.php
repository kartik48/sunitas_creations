<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_empty_cart(): void
    {
        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertViewIs('cart.index');
        $response->assertSee('Your cart is empty');
    }

    public function test_guest_can_add_product_to_cart(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->post(route('cart.add', $product), [
            'quantity' => 2,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to cart!');

        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_authenticated_user_can_add_product_to_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->actingAs($user)->post(route('cart.add', $product), [
            'quantity' => 3,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Product added to cart!');

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_adding_same_product_updates_quantity(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        // First add
        $this->post(route('cart.add', $product), ['quantity' => 2]);

        // Second add should update quantity
        $response = $this->post(route('cart.add', $product), ['quantity' => 3]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Cart updated successfully!');

        $this->assertDatabaseCount('cart_items', 1);
        $this->assertDatabaseHas('cart_items', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_cannot_add_more_than_stock_quantity(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $response = $this->post(route('cart.add', $product), [
            'quantity' => 10,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_cannot_update_quantity_beyond_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        // Add 2 items
        $this->post(route('cart.add', $product), ['quantity' => 2]);

        // Try to add 4 more (total would be 6, but stock is 5)
        $response = $this->post(route('cart.add', $product), ['quantity' => 4]);

        $response->assertRedirect();
        $response->assertSessionHas('error', 'Not enough stock available.');
    }

    public function test_user_can_update_cart_item_quantity(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);
        $cartItem = CartItem::factory()->forSession('test-session')->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        session(['cart_session_id' => 'test-session']);

        $response = $this->patch(route('cart.update', $cartItem), [
            'quantity' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Cart updated!');

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 5,
        ]);
    }

    public function test_user_can_remove_item_from_cart(): void
    {
        $product = Product::factory()->create();
        $cartItem = CartItem::factory()->forSession('test-session')->create([
            'product_id' => $product->id,
        ]);

        session(['cart_session_id' => 'test-session']);

        $response = $this->delete(route('cart.remove', $cartItem));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Item removed from cart!');

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id,
        ]);
    }

    public function test_user_can_clear_entire_cart(): void
    {
        $sessionId = 'test-session';
        CartItem::factory()->forSession($sessionId)->count(3)->create();

        session(['cart_session_id' => $sessionId]);

        $response = $this->delete(route('cart.clear'));

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Cart cleared!');

        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_cart_page_displays_items_correctly(): void
    {
        $product = Product::factory()->create([
            'name' => 'Beautiful Warli Pot',
            'price' => 2500.00,
            'stock_quantity' => 10,
        ]);

        $sessionId = 'test-session';
        CartItem::factory()->forSession($sessionId)->create([
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        session(['cart_session_id' => $sessionId]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('Beautiful Warli Pot');
        $response->assertSee('2,500.00');
        $response->assertSee('5,000.00'); // Subtotal: 2 * 2500
    }

    public function test_cart_calculates_totals_correctly(): void
    {
        $sessionId = 'test-session';
        $product1 = Product::factory()->create(['price' => 1000.00]);
        $product2 = Product::factory()->create(['price' => 2000.00]);

        CartItem::factory()->forSession($sessionId)->create([
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);

        CartItem::factory()->forSession($sessionId)->create([
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);

        session(['cart_session_id' => $sessionId]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('4,000.00'); // Subtotal: (2 * 1000) + (1 * 2000)
        $response->assertSee('FREE'); // Free shipping
    }

    public function test_cart_shows_correct_item_count(): void
    {
        $sessionId = 'test-session';
        CartItem::factory()->forSession($sessionId)->create(['quantity' => 2]);
        CartItem::factory()->forSession($sessionId)->create(['quantity' => 3]);

        session(['cart_session_id' => $sessionId]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('5 items'); // 2 + 3 = 5
    }

    public function test_authenticated_user_cart_is_separate_from_guest_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock_quantity' => 10]);

        // Add as guest
        $this->post(route('cart.add', $product), ['quantity' => 2]);

        // Add as authenticated user
        $this->actingAs($user)->post(route('cart.add', $product), ['quantity' => 3]);

        // Should have 2 separate cart items
        $this->assertDatabaseCount('cart_items', 2);

        // Guest cart
        $this->assertDatabaseHas('cart_items', [
            'user_id' => null,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        // User cart
        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_quantity_must_be_at_least_one(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->post(route('cart.add', $product), [
            'quantity' => 0,
        ]);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_quantity_is_required(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 10]);

        $response = $this->post(route('cart.add', $product), []);

        $response->assertSessionHasErrors('quantity');
    }

    public function test_cart_displays_product_images(): void
    {
        $product = Product::factory()->create();
        \App\Models\ProductImage::factory()->primary()->create([
            'product_id' => $product->id,
            'image_path' => 'products/test-image.jpg',
        ]);

        $sessionId = 'test-session';
        CartItem::factory()->forSession($sessionId)->create([
            'product_id' => $product->id,
        ]);

        session(['cart_session_id' => $sessionId]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        $response->assertSee('products/test-image.jpg');
    }

    public function test_cart_shows_fallback_for_products_without_images(): void
    {
        $product = Product::factory()->create();

        $sessionId = 'test-session';
        CartItem::factory()->forSession($sessionId)->create([
            'product_id' => $product->id,
        ]);

        session(['cart_session_id' => $sessionId]);

        $response = $this->get(route('cart.index'));

        $response->assertStatus(200);
        // Should show SVG fallback
        $this->assertStringContainsString('svg', $response->getContent());
    }
}
