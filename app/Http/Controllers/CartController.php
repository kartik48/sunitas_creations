<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
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
     * Display the shopping cart
     */
    public function index()
    {
        $cartItems = $this->getCartItems();

        $subtotal = $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        $shipping = 0; // Free shipping for now
        $total = $subtotal + $shipping;

        return view('cart.index', compact('cartItems', 'subtotal', 'shipping', 'total'));
    }

    /**
     * Add product to cart
     */
    public function add(Request $request, Product $product)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $product->stock_quantity,
        ]);

        $identifier = $this->getCartIdentifier();

        // Check if item already in cart
        $cartItem = CartItem::where($identifier)
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // Update quantity if already in cart
            $newQuantity = $cartItem->quantity + $request->quantity;

            if ($newQuantity > $product->stock_quantity) {
                return back()->with('error', 'Not enough stock available.');
            }

            $cartItem->update(['quantity' => $newQuantity]);
            $message = 'Cart updated successfully!';
        } else {
            // Add new item to cart
            CartItem::create([
                ...$identifier,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
            $message = 'Product added to cart!';
        }

        return back()->with('success', $message);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $cartItem->product->stock_quantity,
        ]);

        $cartItem->update(['quantity' => $request->quantity]);

        return back()->with('success', 'Cart updated!');
    }

    /**
     * Remove item from cart
     */
    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        $identifier = $this->getCartIdentifier();

        CartItem::where($identifier)->delete();

        return back()->with('success', 'Cart cleared!');
    }

    /**
     * Get cart count for navigation
     */
    public function count()
    {
        $identifier = $this->getCartIdentifier();

        return CartItem::where($identifier)->sum('quantity');
    }
}
