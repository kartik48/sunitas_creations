<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order Confirmation - Sunita's Creations</title>
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success Message -->
        <div class="bg-green-50 border-2 border-green-500 rounded-lg p-8 mb-8 text-center">
            <svg class="w-16 h-16 mx-auto mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <h1 class="text-3xl font-bold text-green-800 mb-2">Order Placed Successfully!</h1>
            <p class="text-green-700">Thank you for your order. We'll send you a confirmation email shortly.</p>
        </div>

        <!-- Order Details -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">Order Details</h2>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-600">Order Number</p>
                    <p class="font-semibold text-lg" style="color: var(--terracotta);">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Order Date</p>
                    <p class="font-semibold">{{ $order->created_at->format('F d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Payment Method</p>
                    <p class="font-semibold">{{ strtoupper($order->payment_method) }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Order Status</p>
                    <p class="font-semibold capitalize">{{ $order->status }}</p>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="border-t pt-4 mb-6">
                <h3 class="font-bold mb-3" style="color: var(--warm-brown);">Customer Information</h3>
                <div class="space-y-1 text-gray-700">
                    <p><span class="font-medium">Name:</span> {{ $order->customer_name }}</p>
                    <p><span class="font-medium">Email:</span> {{ $order->customer_email }}</p>
                    <p><span class="font-medium">Phone:</span> {{ $order->customer_phone }}</p>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="border-t pt-4 mb-6">
                <h3 class="font-bold mb-3" style="color: var(--warm-brown);">Shipping Address</h3>
                <p class="text-gray-700 whitespace-pre-line">{{ $order->shipping_address }}</p>
            </div>

            <!-- Order Items -->
            <div class="border-t pt-4">
                <h3 class="font-bold mb-4" style="color: var(--warm-brown);">Order Items</h3>

                <div class="space-y-4">
                    @foreach($order->items as $item)
                        <div class="flex space-x-4 pb-4 border-b last:border-b-0">
                            @if($item->product->images->where('is_primary', true)->first())
                                <img src="{{ asset('storage/' . $item->product->images->where('is_primary', true)->first()->image_path) }}"
                                    alt="{{ $item->product->name }}"
                                    class="w-20 h-20 object-cover rounded">
                            @endif
                            <div class="flex-1">
                                <h4 class="font-semibold">{{ $item->product->name }}</h4>
                                <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                <p class="text-sm text-gray-600">Price: ₹{{ number_format($item->price, 2) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold" style="color: var(--terracotta);">
                                    ₹{{ number_format($item->price * $item->quantity, 2) }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Order Total -->
                <div class="mt-6 pt-4 border-t">
                    <div class="flex justify-between text-xl font-bold" style="color: var(--warm-brown);">
                        <span>Total Amount</span>
                        <span>₹{{ number_format($order->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            @if($order->notes)
                <div class="border-t pt-4 mt-4">
                    <h3 class="font-bold mb-2" style="color: var(--warm-brown);">Order Notes</h3>
                    <p class="text-gray-700">{{ $order->notes }}</p>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
            <a href="{{ route('shop') }}" class="px-6 py-3 bg-white text-gray-700 font-semibold rounded-lg shadow-md hover:shadow-lg border-2 border-gray-300 transition-shadow duration-200">
                Continue Shopping
            </a>
            <a href="{{ route('home') }}" class="px-6 py-3 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-shadow duration-200"
                style="background-color: var(--terracotta);">
                Back to Home
            </a>
        </div>
    </div>
</body>
</html>
