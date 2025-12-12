<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - Sunita's Creations</title>
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
                    <a href="{{ route('cart.index') }}" class="text-gray-700 hover:text-orange-600 font-medium relative">
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
            Checkout
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form action="{{ route('checkout.store') }}" method="POST" class="bg-white rounded-lg shadow-md p-6">
                    @csrf

                    <!-- Customer Information -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">Customer Information</h2>

                        <div class="space-y-4">
                            <div>
                                <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                                <input type="text" name="customer_name" id="customer_name" value="{{ old('customer_name', auth()->user()->name) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @error('customer_name')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_email" class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                                <input type="email" name="customer_email" id="customer_email" value="{{ old('customer_email', auth()->user()->email) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @error('customer_email')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="customer_phone" class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                                <input type="tel" name="customer_phone" id="customer_phone" value="{{ old('customer_phone') }}" required
                                    placeholder="+91 XXXXX XXXXX"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                                @error('customer_phone')
                                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">Shipping Address</h2>

                        <div>
                            <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-1">Complete Address *</label>
                            <textarea name="shipping_address" id="shipping_address" rows="4" required
                                placeholder="Enter your complete shipping address including street, city, state, and postal code"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('shipping_address') }}</textarea>
                            @error('shipping_address')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">Payment Method</h2>

                        <div class="space-y-3">
                            <div class="flex items-start">
                                <input type="radio" name="payment_method" id="payment_cod" value="cod" checked
                                    class="mt-1 focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300">
                                <label for="payment_cod" class="ml-3 block">
                                    <span class="font-medium text-gray-900">Cash on Delivery (COD)</span>
                                    <p class="text-sm text-gray-500">Pay when you receive your order</p>
                                </label>
                            </div>

                            <div class="flex items-start opacity-50 cursor-not-allowed">
                                <input type="radio" name="payment_method" id="payment_stripe" value="stripe" disabled
                                    class="mt-1 focus:ring-orange-500 h-4 w-4 text-orange-600 border-gray-300">
                                <label for="payment_stripe" class="ml-3 block">
                                    <span class="font-medium text-gray-900">Credit/Debit Card (Coming Soon)</span>
                                    <p class="text-sm text-gray-500">Pay securely online with Stripe</p>
                                </label>
                            </div>
                        </div>
                        @error('payment_method')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Notes -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">Order Notes (Optional)</h2>

                        <div>
                            <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Information</label>
                            <textarea name="notes" id="notes" rows="3"
                                placeholder="Any special instructions for your order?"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">{{ old('notes') }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between pt-4 border-t">
                        <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                            &larr; Back to Cart
                        </a>
                        <button type="submit" class="px-8 py-3 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200"
                            style="background-color: var(--terracotta);">
                            Place Order
                        </button>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">Order Summary</h2>

                    <!-- Cart Items -->
                    <div class="space-y-4 mb-6 max-h-64 overflow-y-auto">
                        @foreach($cartItems as $item)
                            <div class="flex space-x-3">
                                @if($item->product->images->where('is_primary', true)->first())
                                    <img src="{{ asset('storage/' . $item->product->images->where('is_primary', true)->first()->image_path) }}"
                                        alt="{{ $item->product->name }}"
                                        class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="flex-1">
                                    <p class="font-medium text-sm">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    <p class="text-sm font-semibold" style="color: var(--terracotta);">
                                        ₹{{ number_format($item->product->price * $item->quantity, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Price Breakdown -->
                    <div class="border-t pt-4 space-y-2">
                        <div class="flex justify-between text-gray-700">
                            <span>Subtotal</span>
                            <span>₹{{ number_format($subtotal, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-gray-700">
                            <span>Shipping</span>
                            <span class="text-green-600">FREE</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold pt-2 border-t" style="color: var(--warm-brown);">
                            <span>Total</span>
                            <span>₹{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
