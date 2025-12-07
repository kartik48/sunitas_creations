<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Shop - Sunita's Creations</title>
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

        .warli-pattern {
            background-image:
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(200, 92, 63, 0.05) 35px, rgba(200, 92, 63, 0.05) 70px);
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .filter-badge {
            background-color: var(--terracotta);
            color: white;
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
                    <a href="{{ route('shop') }}" class="text-orange-600 hover:text-orange-800 font-medium">Shop</a>
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
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Filters Sidebar -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-24">
                    <h2 class="text-xl font-bold mb-4" style="color: var(--warm-brown);">Filters</h2>

                    <!-- Search -->
                    <form action="{{ route('shop') }}" method="GET" class="mb-6">
                        <input type="text" name="search" placeholder="Search products..."
                               value="{{ request('search') }}"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent">
                        <button type="submit" class="w-full mt-2 px-4 py-2 text-white font-semibold rounded-lg hover:opacity-90 transition" style="background-color: var(--terracotta);">
                            Search
                        </button>
                    </form>

                    <!-- Categories -->
                    <div class="mb-6">
                        <h3 class="font-semibold mb-3" style="color: var(--dark-earth);">Categories</h3>
                        <div class="space-y-2">
                            <a href="{{ route('shop') }}" class="block px-3 py-2 rounded {{ !request('category') ? 'filter-badge' : 'hover:bg-gray-100' }}">
                                All Products
                            </a>
                            @foreach($categories as $category)
                                <a href="{{ route('shop', ['category' => $category->id] + request()->except('category')) }}"
                                   class="block px-3 py-2 rounded {{ request('category') == $category->id ? 'filter-badge' : 'hover:bg-gray-100' }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Tags -->
                    <div>
                        <h3 class="font-semibold mb-3" style="color: var(--dark-earth);">Design & Style</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($tags as $tag)
                                <a href="{{ route('shop', ['tag' => $tag->id] + request()->except('tag')) }}"
                                   class="text-xs px-3 py-1 rounded-full {{ request('tag') == $tag->id ? 'filter-badge' : 'bg-gray-200 hover:bg-gray-300' }}">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    @if(request()->hasAny(['category', 'tag', 'search', 'sort']))
                        <a href="{{ route('shop') }}" class="block mt-6 text-center text-sm text-gray-600 hover:text-orange-600">
                            Clear All Filters
                        </a>
                    @endif
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:col-span-3">
                <!-- Header with sorting -->
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold" style="color: var(--warm-brown);">
                        {{ $products->total() }} Products
                    </h2>
                    <form action="{{ route('shop') }}" method="GET" class="flex items-center gap-2">
                        @foreach(request()->except('sort') as $key => $value)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endforeach
                        <label class="text-sm text-gray-600">Sort by:</label>
                        <select name="sort" onchange="this.form.submit()"
                                class="border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-orange-500">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low to High</option>
                            <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High to Low</option>
                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name</option>
                        </select>
                    </form>
                </div>

                @if($products->count() > 0)
                    <!-- Products Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($products as $product)
                            <div class="bg-white rounded-lg overflow-hidden shadow-lg card-hover">
                                <!-- Product Image -->
                                <a href="{{ route('product.show', $product->slug) }}">
                                    @if($product->primaryImage())
                                        <div class="h-48 overflow-hidden">
                                            <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}"
                                                 alt="{{ $product->name }}"
                                                 class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                        </div>
                                    @else
                                        <div class="h-48 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                                            <svg class="w-16 h-16 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </a>

                                <div class="p-4">
                                    <!-- Tags -->
                                    <div class="flex flex-wrap gap-1 mb-2">
                                        @foreach($product->tags->take(2) as $tag)
                                            <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: var(--cream); color: var(--terracotta);">
                                                {{ $tag->name }}
                                            </span>
                                        @endforeach
                                    </div>

                                    <!-- Product Name -->
                                    <h3 class="text-lg font-semibold mb-2 line-clamp-2" style="color: var(--dark-earth);">
                                        {{ $product->name }}
                                    </h3>

                                    <!-- Category -->
                                    <p class="text-sm text-gray-500 mb-3">{{ $product->category->name }}</p>

                                    <!-- Price and Button -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-xl font-bold" style="color: var(--terracotta);">
                                            ₹{{ number_format($product->price, 2) }}
                                        </span>
                                        <a href="{{ route('product.show', $product->slug) }}" class="px-4 py-2 rounded-lg text-white font-semibold text-sm hover:opacity-90 transition" style="background-color: var(--terracotta);">
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12 bg-white rounded-lg shadow-md">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-gray-500 text-lg">No products found matching your criteria.</p>
                        <a href="{{ route('shop') }}" class="inline-block mt-4 text-orange-600 hover:text-orange-800">
                            Clear filters and browse all products
                        </a>
                    </div>
                @endif
            </div>
        </div>
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
