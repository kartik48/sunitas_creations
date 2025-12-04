<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductAdminTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    private function createRegularUser(): User
    {
        return User::factory()->create(['is_admin' => false]);
    }

    // AUTHORIZATION TESTS

    public function test_guest_cannot_access_admin_product_index(): void
    {
        $response = $this->get(route('admin.products.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_admin_product_index(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('admin.products.index'));

        $response->assertStatus(403); // Forbidden
    }

    public function test_admin_can_access_product_index(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.index');
    }

    public function test_guest_cannot_access_create_product_form(): void
    {
        $response = $this->get(route('admin.products.create'));

        $response->assertRedirect(route('login'));
    }

    public function test_regular_user_cannot_access_create_product_form(): void
    {
        $user = $this->createRegularUser();

        $response = $this->actingAs($user)->get(route('admin.products.create'));

        $response->assertStatus(403);
    }

    public function test_admin_can_access_create_product_form(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.products.create'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.create');
    }

    // CRUD TESTS

    public function test_admin_can_view_products_list(): void
    {
        $admin = $this->createAdmin();
        $products = Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        foreach ($products as $product) {
            $response->assertSee($product->name);
        }
    }

    public function test_admin_can_create_product(): void
    {
        Storage::fake('public');
        $admin = $this->createAdmin();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $productData = [
            'category_id' => $category->id,
            'name' => 'Test Warli Product',
            'description' => 'Beautiful handcrafted item',
            'price' => 1500.00,
            'stock_quantity' => 10,
            'materials' => 'Clay, Natural Dyes',
            'dimensions' => '25 x 25 cm',
            'weight' => '400g',
            'is_featured' => true,
            'is_active' => true,
            'tags' => $tags->pluck('id')->toArray(),
            'images' => [
                UploadedFile::fake()->image('product1.jpg'),
                UploadedFile::fake()->image('product2.jpg'),
            ],
        ];

        $response = $this->actingAs($admin)->post(route('admin.products.store'), $productData);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'name' => 'Test Warli Product',
            'price' => 1500.00,
        ]);

        $product = Product::where('name', 'Test Warli Product')->first();
        $this->assertCount(2, $product->tags);
        $this->assertCount(2, $product->images);

        // Verify images were stored
        Storage::disk('public')->assertExists($product->images->first()->image_path);
    }

    public function test_admin_can_edit_product(): void
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create(['name' => 'Original Name']);

        $response = $this->actingAs($admin)->get(route('admin.products.edit', $product));

        $response->assertStatus(200);
        $response->assertViewIs('admin.products.edit');
        $response->assertSee('Original Name');
    }

    public function test_admin_can_update_product(): void
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create(['name' => 'Old Name', 'price' => 1000]);

        $updateData = [
            'category_id' => $product->category_id,
            'name' => 'Updated Name',
            'description' => $product->description,
            'price' => 2000.00,
            'stock_quantity' => $product->stock_quantity,
            'materials' => $product->materials,
            'dimensions' => $product->dimensions,
            'weight' => $product->weight,
            'is_featured' => $product->is_featured,
            'is_active' => $product->is_active,
            'tags' => [],
        ];

        $response = $this->actingAs($admin)->put(route('admin.products.update', $product), $updateData);

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Name',
            'price' => 2000.00,
        ]);
    }

    public function test_admin_can_delete_product(): void
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create();

        $response = $this->actingAs($admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('products', [
            'id' => $product->id,
        ]);
    }

    public function test_regular_user_cannot_create_product(): void
    {
        $user = $this->createRegularUser();
        $category = Category::factory()->create();

        $productData = [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'description' => 'Test',
            'price' => 1000,
            'stock_quantity' => 10,
            'is_featured' => false,
            'is_active' => true,
            'tags' => [],
        ];

        $response = $this->actingAs($user)->post(route('admin.products.store'), $productData);

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_update_product(): void
    {
        $user = $this->createRegularUser();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->put(route('admin.products.update', $product), [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
    }

    public function test_regular_user_cannot_delete_product(): void
    {
        $user = $this->createRegularUser();
        $product = Product::factory()->create();

        $response = $this->actingAs($user)->delete(route('admin.products.destroy', $product));

        $response->assertStatus(403);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
        ]);
    }

    // VALIDATION TESTS

    public function test_product_creation_requires_required_fields(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), []);

        $response->assertSessionHasErrors(['category_id', 'name', 'price', 'stock_quantity']);
    }

    public function test_product_price_must_be_numeric(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'price' => 'not-a-number',
            'stock_quantity' => 10,
        ]);

        $response->assertSessionHasErrors(['price']);
    }

    public function test_product_stock_quantity_must_be_integer(): void
    {
        $admin = $this->createAdmin();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Test Product',
            'price' => 1000,
            'stock_quantity' => 'not-an-integer',
        ]);

        $response->assertSessionHasErrors(['stock_quantity']);
    }

    // DISPLAY TESTS

    public function test_admin_product_index_shows_empty_state_when_no_products(): void
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('No products yet');
    }

    public function test_admin_can_see_product_details_in_list(): void
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 1500.50,
            'stock_quantity' => 25,
            'is_active' => true
        ]);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Product');
        $response->assertSee('1,500.50');
        $response->assertSee('25');
        $response->assertSee('Active');
    }

    public function test_admin_can_see_primary_image_in_product_list(): void
    {
        $admin = $this->createAdmin();
        $product = Product::factory()->create();
        $primaryImage = ProductImage::factory()->primary()->create(['product_id' => $product->id]);

        $response = $this->actingAs($admin)->get(route('admin.products.index'));

        $response->assertStatus(200);
        $response->assertSee($primaryImage->image_path);
    }
}
