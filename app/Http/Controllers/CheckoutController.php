<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Get the cart identifier (user_id or session_id)
     */
    private function getCartIdentifier()
    {
        if (auth()->check()) {
            return ['user_id' => auth()->id()];
        }

        if (!Session::has('cart_session_id')) {
            Session::put('cart_session_id', Session::getId());
        }

        return ['session_id' => Session::get('cart_session_id')];
    }

    /**
     * Get all cart items for current user/session
     */
    private function getCartItems()
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::with('product.images')
            ->where($identifier)
            ->get();
    }

    /**
     * Display the checkout page
     */
    public function show()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "Not enough stock for {$item->product->name}. Only {$item->product->stock_quantity} available.");
            }
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $shipping = 0; // Free shipping for now
        $total = $subtotal + $shipping;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Process the checkout and create order
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'payment_method' => 'required|in:cod,stripe',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty!');
        }

        // Validate stock and calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            if ($item->quantity > $item->product->stock_quantity) {
                return back()->with('error', "Not enough stock for {$item->product->name}.");
            }
            $total += $item->product->price * $item->quantity;
        }

        // Create order in a transaction
        try {
            DB::beginTransaction();

            // Generate unique order number
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));

            // Create the order
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
                'payment_status' => $validated['payment_method'] === 'cod' ? 'pending' : 'pending',
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'notes' => $validated['notes'] ?? null,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                ]);

                // Decrease product stock
                $cartItem->product->decrement('stock_quantity', $cartItem->quantity);
            }

            // Clear the cart
            $identifier = $this->getCartIdentifier();
            CartItem::where($identifier)->delete();

            DB::commit();

            // Redirect to order confirmation
            return redirect()->route('checkout.confirmation', $order->id)
                ->with('success', 'Order placed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    /**
     * Display order confirmation page
     */
    public function confirmation(Order $order)
    {
        // Ensure user can only see their own orders
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        $order->load('items.product.images');

        return view('checkout.confirmation', compact('order'));
    }

    /**
     * Display user's order history
     */
    public function orders()
    {
        $orders = Order::where('user_id', auth()->id())
            ->with('items.product.images')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }
}
