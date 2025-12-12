<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shopping Cart - Sunita's Creations</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --terracotta: #C85C3F;
            --warm-brown: #8B4513;
            --cream: #F5E6D3;
            --ochre: #CC7722;
            --dark-earth: #3E2723;
        }

        body {
            background-color: var(--cream);
            font-family: 'Georgia', serif;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <a href="{{ route('home') }}">
                        <h1 class="text-2xl font-bold" style="color: var(--terracotta);">
                            Sunita's Creations
                        </h1>
                    </a>
                    <span class="ml-3 text-sm" style="color: var(--warm-brown);">Authentic Warli & Rajasthani Art</span>
                </div>
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 font-medium">Home</a>
                    <a href="{{ route('shop') }}" class="text-gray-700 hover:text-orange-600 font-medium">Shop</a>
                    <a href="{{ route('cart.index') }}" class="text-orange-600 hover:text-orange-800 font-medium relative">
                        <svg class="w-6 h-6 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        @if($cartCount > 0)
                            <span class="absolute -top-2 -right-2 flex items-center justify-center w-5 h-5 text-xs font-bold text-white rounded-full" style="background-color: var(--terracotta);">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.products.index') }}" class="text-gray-700 hover:text-orange-600 font-medium">Admin Panel</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-orange-600 font-medium">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-orange-600 font-medium">Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Title -->
        <h1 class="text-4xl font-bold mb-8" style="color: var(--warm-brown);">
            Shopping Cart
        </h1>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        @if($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        @foreach($cartItems as $item)
                            <div class="flex items-center gap-4 p-6 border-b last:border-b-0">
                                <!-- Product Image -->
                                <div class="w-24 h-24 flex-shrink-0">
                                    @if($item->product->primaryImage())
                                        <img src="{{ asset('storage/' . $item->product->primaryImage()->image_path) }}"
                                             alt="{{ $item->product->name }}"
                                             class="w-full h-full object-cover rounded-lg">
                                    @else
                                        <div class="w-full h-full bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-12 h-12 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold mb-1" style="color: var(--dark-earth);">
                                        <a href="{{ route('product.show', $item->product->slug) }}" class="hover:text-orange-600">
                                            {{ $item->product->name }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-2">{{ $item->product->category->name }}</p>
                                    <p class="text-xl font-bold" style="color: var(--terracotta);">
                                        ₹{{ number_format($item->product->price, 2) }}
                                    </p>
                                </div>

                                <!-- Quantity & Remove -->
                                <div class="flex flex-col gap-2">
                                    <!-- Quantity Update Form -->
                                    <form action="{{ route('cart.update', $item) }}" method="POST" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <label class="text-sm text-gray-600">Qty:</label>
                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                               min="1" max="{{ $item->product->stock_quantity }}"
                                               class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                                        <button type="submit" class="text-sm px-3 py-1 rounded text-white" style="background-color: var(--terracotta);">
                                            Update
                                        </button>
                                    </form>

                                    <!-- Remove Button -->
                                    <form action="{{ route('cart.remove', $item) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                            Remove
                                        </button>
                                    </form>

                                    <!-- Item Subtotal -->
                                    <p class="text-sm text-gray-600">
                                        Subtotal: <span class="font-semibold">₹{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Clear Cart Button -->
                    <div class="mt-4">
                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Are you sure you want to clear your cart?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-red-600 hover:text-red-800">
                                Clear Cart
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                        <h2 class="text-2xl font-bold mb-6" style="color: var(--warm-brown);">
                            Order Summary
                        </h2>

                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-gray-700">
                                <span>Subtotal ({{ $cartItems->sum('quantity') }} items)</span>
                                <span>₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-gray-700">
                                <span>Shipping</span>
                                <span class="text-green-600 font-semibold">FREE</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-xl font-bold" style="color: var(--dark-earth);">
                                <span>Total</span>
                                <span style="color: var(--terracotta);">₹{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        <a href="{{ route('checkout.show') }}" class="block w-full py-3 rounded-lg text-white font-bold text-lg hover:opacity-90 transition mb-3 text-center"
                           style="background-color: var(--terracotta);">
                            Proceed to Checkout
                        </a>

                        <a href="{{ route('shop') }}" class="block text-center text-sm text-gray-600 hover:text-orange-600">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart State -->
            <div class="text-center py-16 bg-white rounded-lg shadow-md">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">
                    Your cart is empty
                </h2>
                <p class="text-gray-600 mb-8">
                    Looks like you haven't added any items to your cart yet.
                </p>
                <a href="{{ route('shop') }}" class="inline-block px-8 py-3 rounded-lg text-white font-semibold hover:opacity-90 transition"
                   style="background-color: var(--terracotta);">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>

    <!-- Footer -->
    <footer class="mt-16 py-8 text-white" style="background-color: var(--warm-brown);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold mb-2">Sunita's Creations</h3>
            <p class="text-orange-200 mb-4">Preserving tradition, one masterpiece at a time</p>
            <p class="text-sm text-orange-200">&copy; {{ date('Y') }} Sunita's Creations. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
