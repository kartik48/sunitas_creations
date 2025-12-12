<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Orders - Sunita's Creations</title>
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

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-pending {
            background-color: #FEF3C7;
            color: #92400E;
        }

        .status-processing {
            background-color: #DBEAFE;
            color: #1E40AF;
        }

        .status-completed {
            background-color: #D1FAE5;
            color: #065F46;
        }

        .status-cancelled {
            background-color: #FEE2E2;
            color: #991B1B;
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
                        <a href="{{ route('orders.index') }}" class="text-orange-600 hover:text-orange-800 font-medium">My Orders</a>
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
            My Orders
        </h1>

        @if($orders->count() > 0)
            <!-- Orders List -->
            <div class="space-y-6">
                @foreach($orders as $order)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Order Header -->
                        <div class="bg-gray-50 px-6 py-4 border-b flex justify-between items-center">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 flex-1">
                                <div>
                                    <p class="text-sm text-gray-600">Order Number</p>
                                    <p class="font-semibold" style="color: var(--terracotta);">{{ $order->order_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Date</p>
                                    <p class="font-semibold">{{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Total</p>
                                    <p class="font-semibold">₹{{ number_format($order->total_amount, 2) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600">Status</p>
                                    <span class="status-badge status-{{ $order->status }}">{{ $order->status }}</span>
                                </div>
                            </div>
                            <a href="{{ route('checkout.confirmation', $order->id) }}" class="ml-4 text-sm text-orange-600 hover:text-orange-800 font-medium whitespace-nowrap">
                                View Details &rarr;
                            </a>
                        </div>

                        <!-- Order Items -->
                        <div class="px-6 py-4">
                            <div class="space-y-4">
                                @foreach($order->items as $item)
                                    <div class="flex items-center space-x-4">
                                        @if($item->product->images->where('is_primary', true)->first())
                                            <img src="{{ asset('storage/' . $item->product->images->where('is_primary', true)->first()->image_path) }}"
                                                alt="{{ $item->product->name }}"
                                                class="w-16 h-16 object-cover rounded">
                                        @else
                                            <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900">{{ $item->product->name }}</h4>
                                            <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-semibold" style="color: var(--terracotta);">
                                                ₹{{ number_format($item->price * $item->quantity, 2) }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if($orders->hasPages())
                <div class="mt-8">
                    {{ $orders->links() }}
                </div>
            @endif
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white rounded-lg shadow-md">
                <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h2 class="text-2xl font-bold mb-4" style="color: var(--warm-brown);">
                    No Orders Yet
                </h2>
                <p class="text-gray-600 mb-8">
                    You haven't placed any orders yet. Start shopping to see your orders here.
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
