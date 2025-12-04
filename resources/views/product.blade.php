<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} - Sunita's Creations</title>
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

        .thumbnail {
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .thumbnail:hover {
            transform: scale(1.05);
            border-color: var(--terracotta);
        }

        .thumbnail.active {
            border-color: var(--terracotta);
            border-width: 3px;
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
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
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 font-medium">Home</a>
                    <a href="{{ route('shop') }}" class="text-gray-700 hover:text-orange-600 font-medium">Shop</a>
                    <a href="#" class="text-gray-700 hover:text-orange-600 font-medium">About</a>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.products.index') }}" class="text-orange-600 hover:text-orange-800 font-medium">Admin Panel</a>
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
        <!-- Breadcrumb -->
        <div class="mb-6 text-sm">
            <a href="{{ route('home') }}" class="text-gray-600 hover:text-orange-600">Home</a>
            <span class="mx-2 text-gray-400">/</span>
            <a href="{{ route('shop') }}" class="text-gray-600 hover:text-orange-600">Shop</a>
            <span class="mx-2 text-gray-400">/</span>
            <a href="{{ route('shop', ['category' => $product->category->id]) }}" class="text-gray-600 hover:text-orange-600">{{ $product->category->name }}</a>
            <span class="mx-2 text-gray-400">/</span>
            <span class="text-gray-900">{{ $product->name }}</span>
        </div>

        <!-- Product Details Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 mb-16">
            <!-- Image Gallery -->
            <div>
                <!-- Main Image -->
                <div class="bg-white rounded-lg shadow-lg overflow-hidden mb-4">
                    @if($product->images->count() > 0)
                        <img id="mainImage"
                             src="{{ asset('storage/' . $product->primaryImage()->image_path) }}"
                             alt="{{ $product->name }}"
                             class="w-full h-96 object-cover">
                    @else
                        <div class="w-full h-96 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                            <svg class="w-24 h-24 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    @endif
                </div>

                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($product->images as $index => $image)
                            <div class="bg-white rounded-lg overflow-hidden border-2 thumbnail {{ $index === 0 ? 'active' : 'border-gray-200' }}"
                                 onclick="changeImage('{{ asset('storage/' . $image->image_path) }}', this)">
                                <img src="{{ asset('storage/' . $image->image_path) }}"
                                     alt="{{ $product->name }}"
                                     class="w-full h-20 object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <!-- Tags -->
                <div class="flex flex-wrap gap-2 mb-3">
                    @foreach($product->tags as $tag)
                        <span class="text-xs px-3 py-1 rounded-full" style="background-color: var(--cream); color: var(--terracotta); border: 1px solid var(--terracotta);">
                            {{ $tag->name }}
                        </span>
                    @endforeach
                </div>

                <!-- Product Name -->
                <h1 class="text-4xl font-bold mb-2" style="color: var(--dark-earth);">
                    {{ $product->name }}
                </h1>

                <!-- Category -->
                <p class="text-lg text-gray-600 mb-4">{{ $product->category->name }}</p>

                <!-- Price -->
                <div class="mb-6">
                    <span class="text-4xl font-bold" style="color: var(--terracotta);">
                        ₹{{ number_format($product->price, 2) }}
                    </span>
                </div>

                <!-- Stock Status -->
                <div class="mb-6">
                    @if($product->stock_quantity > 0)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            In Stock ({{ $product->stock_quantity }} available)
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="text-lg font-semibold mb-2" style="color: var(--warm-brown);">Description</h3>
                    <p class="text-gray-700 leading-relaxed">{{ $product->description }}</p>
                </div>

                <!-- Product Details -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h3 class="text-lg font-semibold mb-4" style="color: var(--warm-brown);">Product Details</h3>
                    <div class="space-y-2">
                        @if($product->materials)
                            <div class="flex">
                                <span class="font-semibold text-gray-700 w-32">Materials:</span>
                                <span class="text-gray-600">{{ $product->materials }}</span>
                            </div>
                        @endif
                        @if($product->dimensions)
                            <div class="flex">
                                <span class="font-semibold text-gray-700 w-32">Dimensions:</span>
                                <span class="text-gray-600">{{ $product->dimensions }}</span>
                            </div>
                        @endif
                        @if($product->weight)
                            <div class="flex">
                                <span class="font-semibold text-gray-700 w-32">Weight:</span>
                                <span class="text-gray-600">{{ $product->weight }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Add to Cart Button -->
                @if($product->stock_quantity > 0)
                    <button class="w-full py-4 rounded-lg text-white font-bold text-lg hover:opacity-90 transition"
                            style="background-color: var(--terracotta);">
                        Add to Cart
                    </button>
                @else
                    <button disabled class="w-full py-4 rounded-lg text-white font-bold text-lg opacity-50 cursor-not-allowed"
                            style="background-color: var(--terracotta);">
                        Out of Stock
                    </button>
                @endif
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mb-16">
                <h2 class="text-3xl font-bold mb-8 text-center" style="color: var(--warm-brown);">
                    You May Also Like
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg overflow-hidden shadow-lg card-hover">
                            <a href="{{ route('product.show', $relatedProduct->slug) }}">
                                @if($relatedProduct->primaryImage())
                                    <div class="h-48 overflow-hidden">
                                        <img src="{{ asset('storage/' . $relatedProduct->primaryImage()->image_path) }}"
                                             alt="{{ $relatedProduct->name }}"
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
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($relatedProduct->tags->take(2) as $tag)
                                        <span class="text-xs px-2 py-0.5 rounded-full" style="background-color: var(--cream); color: var(--terracotta);">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>

                                <h3 class="text-lg font-semibold mb-2 line-clamp-2" style="color: var(--dark-earth);">
                                    {{ $relatedProduct->name }}
                                </h3>

                                <p class="text-sm text-gray-500 mb-3">{{ $relatedProduct->category->name }}</p>

                                <div class="flex justify-between items-center">
                                    <span class="text-xl font-bold" style="color: var(--terracotta);">
                                        ₹{{ number_format($relatedProduct->price, 2) }}
                                    </span>
                                    <a href="{{ route('product.show', $relatedProduct->slug) }}"
                                       class="px-4 py-2 rounded-lg text-white font-semibold text-sm hover:opacity-90 transition"
                                       style="background-color: var(--terracotta);">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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

    <script>
        function changeImage(src, element) {
            document.getElementById('mainImage').src = src;

            // Remove active class from all thumbnails
            document.querySelectorAll('.thumbnail').forEach(thumb => {
                thumb.classList.remove('active');
                thumb.classList.add('border-gray-200');
            });

            // Add active class to clicked thumbnail
            element.classList.add('active');
            element.classList.remove('border-gray-200');
        }
    </script>
</body>
</html>
