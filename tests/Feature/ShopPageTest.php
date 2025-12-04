<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_shop_page_loads_successfully(): void
    {
        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        $response->assertViewIs('shop');
    }

    public function test_shop_displays_active_products(): void
    {
        $activeProducts = Product::factory()
            ->count(3)
            ->create(['is_active' => true]);

        // Inactive products should not appear
        Product::factory()
            ->count(2)
            ->create(['is_active' => false]);

        $response = $this->get(route('shop'));

        $response->assertStatus(200);

        foreach ($activeProducts as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_shop_can_filter_by_category(): void
    {
        $category1 = Category::factory()->create(['name' => 'Wall Decor']);
        $category2 = Category::factory()->create(['name' => 'Dry Fruit Holders']);

        $productsInCategory1 = Product::factory()
            ->count(2)
            ->create(['category_id' => $category1->id]);

        $productsInCategory2 = Product::factory()
            ->count(2)
            ->create(['category_id' => $category2->id]);

        $response = $this->get(route('shop', ['category' => $category1->id]));

        $response->assertStatus(200);

        // Should see products from category 1
        foreach ($productsInCategory1 as $product) {
            $response->assertSee($product->name);
        }

        // Should NOT see products from category 2
        foreach ($productsInCategory2 as $product) {
            $response->assertDontSee($product->name);
        }
    }

    public function test_shop_can_filter_by_tag(): void
    {
        $warliTag = Tag::factory()->create(['name' => 'Warli']);
        $rajasthaniTag = Tag::factory()->create(['name' => 'Rajasthani']);

        $warliProduct = Product::factory()->create();
        $warliProduct->tags()->attach($warliTag);

        $rajasthaniProduct = Product::factory()->create();
        $rajasthaniProduct->tags()->attach($rajasthaniTag);

        $response = $this->get(route('shop', ['tag' => $warliTag->id]));

        $response->assertStatus(200);
        $response->assertSee($warliProduct->name);
        $response->assertDontSee($rajasthaniProduct->name);
    }

    public function test_shop_can_search_products(): void
    {
        Product::factory()->create(['name' => 'Warli Clay Pot']);
        Product::factory()->create(['name' => 'Rajasthani Wall Hanging']);

        $response = $this->get(route('shop', ['search' => 'Warli']));

        $response->assertStatus(200);
        $response->assertSee('Warli Clay Pot');
        $response->assertDontSee('Rajasthani Wall Hanging');
    }

    public function test_shop_can_sort_by_price_low_to_high(): void
    {
        Product::factory()->create(['name' => 'Expensive Item', 'price' => 5000]);
        Product::factory()->create(['name' => 'Cheap Item', 'price' => 100]);

        $response = $this->get(route('shop', ['sort' => 'price_low']));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Cheap Item', 'Expensive Item']);
    }

    public function test_shop_can_sort_by_price_high_to_low(): void
    {
        Product::factory()->create(['name' => 'Expensive Item', 'price' => 5000]);
        Product::factory()->create(['name' => 'Cheap Item', 'price' => 100]);

        $response = $this->get(route('shop', ['sort' => 'price_high']));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Expensive Item', 'Cheap Item']);
    }

    public function test_shop_can_sort_by_name(): void
    {
        Product::factory()->create(['name' => 'Zebra Item']);
        Product::factory()->create(['name' => 'Apple Item']);

        $response = $this->get(route('shop', ['sort' => 'name']));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['Apple Item', 'Zebra Item']);
    }

    public function test_shop_sorts_by_newest_by_default(): void
    {
        $oldProduct = Product::factory()->create([
            'name' => 'Old Product',
            'created_at' => now()->subDays(5)
        ]);

        $newProduct = Product::factory()->create([
            'name' => 'New Product',
            'created_at' => now()
        ]);

        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        $response->assertSeeInOrder(['New Product', 'Old Product']);
    }

    public function test_shop_shows_empty_state_when_no_products(): void
    {
        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        $response->assertSee('No products found matching your criteria');
    }

    public function test_shop_combines_multiple_filters(): void
    {
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();

        $matchingProduct = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Matching Warli Product',
            'price' => 500
        ]);
        $matchingProduct->tags()->attach($tag);

        $nonMatchingProduct = Product::factory()->create([
            'name' => 'Other Product'
        ]);

        $response = $this->get(route('shop', [
            'category' => $category->id,
            'tag' => $tag->id,
            'search' => 'Warli',
            'sort' => 'price_low'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Matching Warli Product');
        $response->assertDontSee('Other Product');
    }

    public function test_shop_displays_product_count(): void
    {
        Product::factory()->count(5)->create();

        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        $response->assertSee('5 Products');
    }

    public function test_shop_displays_categories_filter(): void
    {
        $categories = Category::factory()->count(3)->create();

        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        foreach ($categories as $category) {
            $response->assertSee($category->name);
        }
    }

    public function test_shop_displays_tags_filter(): void
    {
        $tags = Tag::factory()->count(3)->create();

        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        foreach ($tags as $tag) {
            $response->assertSee($tag->name);
        }
    }

    public function test_shop_product_cards_display_correctly(): void
    {
        $product = Product::factory()
            ->has(ProductImage::factory()->primary(), 'images')
            ->create(['price' => 1500.50]);

        $tag = Tag::factory()->create(['name' => 'Handmade']);
        $product->tags()->attach($tag);

        $response = $this->get(route('shop'));

        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee($product->category->name);
        $response->assertSee('1,500.50');
        $response->assertSee('Handmade');
        $response->assertSee('View Details');
    }
}
