<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertViewIs('home');
    }

    public function test_homepage_displays_featured_products(): void
    {
        // Create some featured products
        $featuredProducts = Product::factory()
            ->count(3)
            ->featured()
            ->has(ProductImage::factory()->primary(), 'images')
            ->create();

        // Create non-featured products (should not appear)
        Product::factory()
            ->count(2)
            ->create(['is_featured' => false]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);

        // Check that featured products are displayed
        foreach ($featuredProducts as $product) {
            $response->assertSee($product->name);
            $response->assertSee(number_format($product->price, 2));
        }
    }

    public function test_homepage_displays_categories(): void
    {
        $categories = Category::factory()->count(4)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);

        foreach ($categories as $category) {
            $response->assertSee($category->name);
            $response->assertSee($category->description);
        }
    }

    public function test_homepage_shows_empty_state_when_no_featured_products(): void
    {
        // Create only non-featured products
        Product::factory()->count(3)->create(['is_featured' => false]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('No featured products yet');
    }

    public function test_homepage_product_links_are_correct(): void
    {
        $product = Product::factory()
            ->featured()
            ->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee(route('product.show', $product->slug));
    }

    public function test_homepage_category_links_filter_shop(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee(route('shop', ['category' => $category->id]));
    }

    public function test_homepage_navigation_links_are_present(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Sunita\'s Creations', false); // false = don't escape HTML
        $response->assertSee('Shop');
        $response->assertSee('Home');
    }

    public function test_homepage_shows_correct_footer(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Preserving tradition, one masterpiece at a time');
        $response->assertSee(date('Y'));
    }
}
