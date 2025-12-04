<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sunita's Creations - Authentic Warli & Rajasthani Handicrafts</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Warli-inspired color palette */
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

        /* Warli art pattern border */
        .warli-border {
            border: 3px solid var(--terracotta);
            position: relative;
        }

        .warli-border::before,
        .warli-border::after {
            content: '';
            position: absolute;
            background: var(--cream);
        }

        /* Warli geometric pattern background */
        .warli-pattern {
            background-image:
                repeating-linear-gradient(45deg, transparent, transparent 35px, rgba(200, 92, 63, 0.05) 35px, rgba(200, 92, 63, 0.05) 70px);
        }

        /* Warli triangle pattern */
        .warli-triangles {
            background-image:
                linear-gradient(135deg, var(--terracotta) 25%, transparent 25%),
                linear-gradient(225deg, var(--terracotta) 25%, transparent 25%),
                linear-gradient(45deg, var(--terracotta) 25%, transparent 25%),
                linear-gradient(315deg, var(--terracotta) 25%, transparent 25%);
            background-position: 10px 0, 10px 0, 0 0, 0 0;
            background-size: 10px 10px;
            background-repeat: repeat;
            opacity: 0.1;
        }

        /* Warli dancing figures pattern (simplified) */
        .warli-dancers {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23C85C3F' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='8' r='3'/%3E%3Cline x1='30' y1='11' x2='30' y2='25' stroke='%23C85C3F' stroke-width='1.5'/%3E%3Cline x1='30' y1='15' x2='22' y2='20' stroke='%23C85C3F' stroke-width='1.5'/%3E%3Cline x1='30' y1='15' x2='38' y2='20' stroke='%23C85C3F' stroke-width='1.5'/%3E%3Cline x1='30' y1='25' x2='22' y2='35' stroke='%23C85C3F' stroke-width='1.5'/%3E%3Cline x1='30' y1='25' x2='38' y2='35' stroke='%23C85C3F' stroke-width='1.5'/%3E%3C/g%3E%3C/svg%3E");
        }

        .hero-section {
            background: linear-gradient(135deg, var(--warm-brown) 0%, var(--ochre) 100%);
        }

        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .warli-divider {
            height: 3px;
            background: repeating-linear-gradient(
                90deg,
                var(--terracotta),
                var(--terracotta) 10px,
                transparent 10px,
                transparent 20px
            );
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="bg-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center">
                    <h1 class="text-2xl font-bold" style="color: var(--terracotta);">
                        Sunita's Creations
                    </h1>
                    <span class="ml-3 text-sm" style="color: var(--warm-brown);">Authentic Warli & Rajasthani Art</span>
                </div>
                <div class="hidden md:flex space-x-8">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-orange-600 font-medium">Home</a>
                    <a href="#" class="text-gray-700 hover:text-orange-600 font-medium">Shop</a>
                    <a href="#categories" class="text-gray-700 hover:text-orange-600 font-medium">Categories</a>
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

    <!-- Hero Section with Warli Art -->
    <section class="hero-section warli-dancers relative py-20">
        <div class="absolute inset-0 warli-triangles"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <h2 class="text-5xl font-bold text-white mb-4">
                Handcrafted with Love & Tradition
            </h2>
            <p class="text-xl text-orange-100 mb-8 max-w-2xl mx-auto">
                Discover authentic Warli and Rajasthani handicrafts, each piece telling a story of India's rich cultural heritage
            </p>
            <div class="flex justify-center gap-4">
                <a href="#featured" class="bg-white text-orange-600 px-8 py-3 rounded-lg font-semibold hover:bg-orange-50 transition">
                    Explore Collection
                </a>
                <a href="#categories" class="border-2 border-white text-white px-8 py-3 rounded-lg font-semibold hover:bg-white hover:text-orange-600 transition">
                    Shop by Category
                </a>
            </div>
        </div>
    </section>

    <!-- Warli Divider -->
    <div class="warli-divider my-8"></div>

    <!-- Categories Section -->
    <section id="categories" class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-12" style="color: var(--warm-brown);">
                Shop by Category
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($categories as $category)
                    <a href="{{ route('shop', ['category' => $category->id]) }}" class="group">
                        <div class="warli-border bg-cream p-6 rounded-lg text-center card-hover">
                            <div class="w-16 h-16 mx-auto mb-4 rounded-full flex items-center justify-center" style="background-color: var(--terracotta);">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold mb-2" style="color: var(--dark-earth);">{{ $category->name }}</h3>
                            <p class="text-sm text-gray-600">{{ $category->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Warli Divider -->
    <div class="warli-divider my-8"></div>

    <!-- Featured Products -->
    <section id="featured" class="py-16 warli-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-bold text-center mb-4" style="color: var(--warm-brown);">
                Featured Creations
            </h2>
            <p class="text-center text-gray-600 mb-12">Handpicked masterpieces showcasing traditional Warli & Rajasthani artistry</p>

            @if($featuredProducts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($featuredProducts as $product)
                        <div class="bg-white rounded-lg overflow-hidden shadow-lg card-hover">
                            <!-- Product Image -->
                            <a href="{{ route('product.show', $product->slug) }}">
                                @if($product->primaryImage())
                                    <div class="h-64 overflow-hidden">
                                        <img src="{{ asset('storage/' . $product->primaryImage()->image_path) }}"
                                             alt="{{ $product->name }}"
                                             class="w-full h-full object-cover hover:scale-110 transition-transform duration-300">
                                    </div>
                                @else
                                    <div class="h-64 bg-gradient-to-br from-orange-100 to-orange-200 flex items-center justify-center">
                                        <svg class="w-24 h-24 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </a>

                            <div class="p-6">
                                <div class="flex flex-wrap gap-1 mb-2">
                                    @foreach($product->tags->take(3) as $tag)
                                        <span class="text-xs px-2 py-1 rounded-full" style="background-color: var(--cream); color: var(--terracotta);">
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach
                                </div>

                                <h3 class="text-xl font-semibold mb-2" style="color: var(--dark-earth);">
                                    {{ $product->name }}
                                </h3>

                                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                    {{ Str::limit($product->description, 100) }}
                                </p>

                                <div class="flex justify-between items-center">
                                    <span class="text-2xl font-bold" style="color: var(--terracotta);">
                                        ₹{{ number_format($product->price, 2) }}
                                    </span>
                                    <a href="{{ route('product.show', $product->slug) }}" class="px-4 py-2 rounded-lg text-white font-semibold hover:opacity-90 transition" style="background-color: var(--terracotta);">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500 text-lg">No featured products yet. Check back soon!</p>
                    @auth
                        @if(auth()->user()->is_admin)
                            <a href="{{ route('admin.products.create') }}" class="inline-block mt-4 px-6 py-3 rounded-lg text-white font-semibold" style="background-color: var(--terracotta);">
                                Add Featured Products
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </section>

    <!-- About Section with Warli Elements -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-4xl font-bold mb-6" style="color: var(--warm-brown);">
                        The Art of Warli
                    </h2>
                    <p class="text-gray-700 mb-4">
                        Each piece in our collection celebrates the timeless beauty of Warli art - a traditional Indian folk art form characterized by simple geometric patterns that tell stories of daily life, nature, and celebrations.
                    </p>
                    <p class="text-gray-700 mb-6">
                        Combined with Rajasthani craftsmanship, every creation is a unique masterpiece, handcrafted with love by artisan Sunita and her team.
                    </p>
                    <div class="flex gap-4">
                        <div class="text-center">
                            <div class="text-3xl font-bold" style="color: var(--terracotta);">100%</div>
                            <div class="text-sm text-gray-600">Handmade</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold" style="color: var(--terracotta);">Eco</div>
                            <div class="text-sm text-gray-600">Friendly</div>
                        </div>
                        <div class="text-center">
                            <div class="text-3xl font-bold" style="color: var(--terracotta);">∞</div>
                            <div class="text-sm text-gray-600">Tradition</div>
                        </div>
                    </div>
                </div>
                <div class="warli-border rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ asset('worli_artwork.png') }}" alt="Authentic Warli Artwork" class="w-full h-full object-cover">
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-8 text-white" style="background-color: var(--warm-brown);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold mb-2">Sunita's Creations</h3>
            <p class="text-orange-200 mb-4">Preserving tradition, one masterpiece at a time</p>
            <div class="warli-divider my-4 mx-auto" style="width: 200px; background: repeating-linear-gradient(90deg, white, white 10px, transparent 10px, transparent 20px);"></div>
            <p class="text-sm text-orange-200">&copy; {{ date('Y') }} Sunita's Creations. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>
