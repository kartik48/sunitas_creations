<?php

namespace Tests\Feature;

use App\Models\CartItem;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();

        // Only disable CSRF protection, not all middleware
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

        $this->user = User::factory()->create();
        $category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 100.00,
            'stock_quantity' => 10,
        ]);
    }

    public function test_checkout_page_requires_authentication()
    {
        $response = $this->get(route('checkout.show'));

        $response->assertRedirect(route('login'));
    }

    public function test_checkout_page_displays_for_authenticated_user_with_cart_items()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->get(route('checkout.show'));

        $response->assertOk();
        $response->assertSee('Checkout', false);
        $response->assertSee($this->product->name, false);
        $response->assertSee('200.00'); // 2 x 100
    }

    public function test_checkout_page_redirects_when_cart_is_empty()
    {
        $response = $this->actingAs($this->user)->get(route('checkout.show'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Your cart is empty!');
    }

    public function test_checkout_redirects_when_insufficient_stock()
    {
        $this->product->update(['stock_quantity' => 1]);

        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        $response = $this->actingAs($this->user)->get(route('checkout.show'));

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error');
    }

    public function test_checkout_validates_required_fields()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), []);

        $response->assertSessionHasErrors([
            'customer_name',
            'customer_email',
            'customer_phone',
            'shipping_address',
            'payment_method',
        ]);
    }

    public function test_checkout_validates_email_format()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'invalid-email',
            'customer_phone' => '1234567890',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHasErrors(['customer_email']);
    }

    public function test_checkout_validates_payment_method()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '123 Main St',
            'payment_method' => 'invalid-method',
        ]);

        $response->assertSessionHasErrors(['payment_method']);
    }

    public function test_successful_checkout_creates_order()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St, City, State, 12345',
            'payment_method' => 'cod',
            'notes' => 'Please deliver before 5pm',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'total_amount' => 200.00,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
        ]);

        $order = Order::where('user_id', $this->user->id)->first();
        $response->assertRedirect(route('checkout.confirmation', $order->id));
    }

    public function test_successful_checkout_creates_order_items()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $this->user->id)->first();

        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);
    }

    public function test_successful_checkout_updates_product_stock()
    {
        $initialStock = $this->product->stock_quantity;

        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
        ]);

        $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $this->product->refresh();
        $this->assertEquals($initialStock - 3, $this->product->stock_quantity);
    }

    public function test_successful_checkout_clears_cart()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        $this->assertEquals(1, CartItem::where('user_id', $this->user->id)->count());

        $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $this->assertEquals(0, CartItem::where('user_id', $this->user->id)->count());
    }

    public function test_checkout_generates_unique_order_number()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $this->user->id)->first();

        $this->assertNotNull($order->order_number);
        $this->assertStringStartsWith('ORD-', $order->order_number);
    }

    public function test_checkout_fails_when_cart_is_empty()
    {
        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $response->assertRedirect(route('cart.index'));
        $response->assertSessionHas('error', 'Your cart is empty!');
    }

    public function test_checkout_fails_when_stock_becomes_insufficient()
    {
        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
        ]);

        // Simulate stock change between viewing checkout and submitting
        $this->product->update(['stock_quantity' => 2]);

        $response = $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(0, Order::count());
    }

    public function test_order_confirmation_page_displays_order_details()
    {
        $order = Order::create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-TEST123',
            'total_amount' => 500.00,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'pending',
            'customer_name' => 'Test User',
            'customer_email' => 'test@example.com',
            'customer_phone' => '1234567890',
            'shipping_address' => '123 Test St',
        ]);

        $response = $this->actingAs($this->user)->get(route('checkout.confirmation', $order->id));

        $response->assertOk();
        $response->assertSee('Order Placed Successfully!', false);
        $response->assertSee('ORD-TEST123', false);
        $response->assertSee('500.00');
    }

    public function test_order_confirmation_page_requires_order_ownership()
    {
        $otherUser = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('checkout.confirmation', $order->id));

        $response->assertForbidden();
    }

    public function test_order_history_page_displays_user_orders()
    {
        $orders = Order::factory()->count(3)->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('My Orders', false);
        foreach ($orders as $order) {
            $response->assertSee($order->order_number, false);
        }
    }

    public function test_order_history_page_shows_only_user_orders()
    {
        $otherUser = User::factory()->create();

        Order::factory()->create([
            'user_id' => $this->user->id,
            'order_number' => 'ORD-USER123',
        ]);

        Order::factory()->create([
            'user_id' => $otherUser->id,
            'order_number' => 'ORD-OTHER123',
        ]);

        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertSee('ORD-USER123', false);
        $response->assertDontSee('ORD-OTHER123', false);
    }

    public function test_order_history_shows_empty_state_when_no_orders()
    {
        $response = $this->actingAs($this->user)->get(route('orders.index'));

        $response->assertOk();
        $response->assertSee('No Orders Yet', false);
        $response->assertSee('Start Shopping', false);
    }

    public function test_checkout_handles_multiple_products_in_cart()
    {
        $category = Category::factory()->create();
        $product2 = Product::factory()->create([
            'category_id' => $category->id,
            'price' => 150.00,
            'stock_quantity' => 10,
        ]);

        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);

        CartItem::create([
            'user_id' => $this->user->id,
            'product_id' => $product2->id,
            'quantity' => 1,
        ]);

        $this->actingAs($this->user)->post(route('checkout.store'), [
            'customer_name' => 'John Doe',
            'customer_email' => 'john@example.com',
            'customer_phone' => '+91 9876543210',
            'shipping_address' => '123 Main St',
            'payment_method' => 'cod',
        ]);

        $order = Order::where('user_id', $this->user->id)->first();

        $this->assertEquals(350.00, $order->total_amount); // (2 * 100) + (1 * 150)
        $this->assertEquals(2, $order->items()->count());
    }
}
