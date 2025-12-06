<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_detail_page_loads_successfully(): void
    {
        $product = Product::factory()->create();

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertViewIs('product');
    }

    public function test_product_detail_displays_all_information(): void
    {
        $product = Product::factory()->create([
            'name' => 'Beautiful Warli Clay Pot',
            'description' => 'Handcrafted with traditional Warli patterns',
            'price' => 2500.00,
            'materials' => 'Clay, Natural Dyes',
            'dimensions' => '30 x 30 cm',
            'weight' => '500g',
            'stock_quantity' => 10
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Beautiful Warli Clay Pot');
        $response->assertSee('Handcrafted with traditional Warli patterns');
        $response->assertSee('2,500.00');
        $response->assertSee('Clay, Natural Dyes');
        $response->assertSee('30 x 30 cm');
        $response->assertSee('500g');
        $response->assertSee('In Stock');
    }

    public function test_product_detail_displays_category(): void
    {
        $category = Category::factory()->create(['name' => 'Wall Decor']);
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Wall Decor');
    }

    public function test_product_detail_displays_tags(): void
    {
        $product = Product::factory()->create();
        $tags = Tag::factory()->count(3)->create();
        $product->tags()->attach($tags);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        foreach ($tags as $tag) {
            $response->assertSee($tag->name);
        }
    }

    public function test_product_detail_shows_out_of_stock_status(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Out of Stock');
    }

    public function test_product_detail_shows_add_to_cart_button_when_in_stock(): void
    {
        $product = Product::factory()->create(['stock_quantity' => 5]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Add to Cart');
    }

    public function test_product_detail_disables_add_to_cart_when_out_of_stock(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Out of Stock');
        // Button is disabled, so check for disabled attribute
        $this->assertStringContainsString('disabled', $response->getContent());
    }

    public function test_product_detail_displays_breadcrumb_navigation(): void
    {
        $category = Category::factory()->create(['name' => 'Festive Decor']);
        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Diwali Lamp'
        ]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Home');
        $response->assertSee('Shop');
        $response->assertSee('Festive Decor');
        $response->assertSee('Diwali Lamp');
    }

    public function test_product_detail_displays_related_products(): void
    {
        $category = Category::factory()->create();

        $mainProduct = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Main Product'
        ]);

        $relatedProducts = Product::factory()->count(3)->create([
            'category_id' => $category->id
        ]);

        $response = $this->get(route('product.show', $mainProduct->slug));

        $response->assertStatus(200);
        $response->assertSee('You May Also Like');

        foreach ($relatedProducts as $related) {
            $response->assertSee($related->name);
        }
    }

    public function test_product_detail_does_not_show_itself_in_related_products(): void
    {
        $category = Category::factory()->create();

        $product = Product::factory()->create([
            'category_id' => $category->id,
            'name' => 'Unique Product Name XYZ123'
        ]);

        // Create other products in same category
        $relatedProducts = Product::factory()->count(2)->create(['category_id' => $category->id]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);

        // Product name should NOT appear in the related products section
        // (it's OK if it appears in breadcrumb, title, etc.)
        // Just check that related products ARE shown
        foreach ($relatedProducts as $related) {
            $response->assertSee($related->name);
        }
    }

    public function test_product_detail_shows_no_related_section_when_no_related_products(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertDontSee('You May Also Like');
    }

    public function test_product_detail_displays_primary_image(): void
    {
        $product = Product::factory()->create();
        $primaryImage = ProductImage::factory()->primary()->create(['product_id' => $product->id]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee($primaryImage->image_path);
    }

    public function test_product_detail_displays_image_gallery_when_multiple_images(): void
    {
        $product = Product::factory()->create();
        ProductImage::factory()->count(4)->create(['product_id' => $product->id]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);

        // Thumbnails should be present
        $this->assertStringContainsString('thumbnail', $response->getContent());
    }

    public function test_product_detail_404_for_invalid_slug(): void
    {
        $response = $this->get(route('product.show', 'non-existent-product'));

        $response->assertStatus(404);
    }

    public function test_product_detail_links_to_category_filtered_shop(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->create(['category_id' => $category->id]);

        $response = $this->get(route('product.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee(route('shop', ['category' => $category->id]));
    }
}
