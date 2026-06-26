<?php

namespace Tests\Feature\Api;

use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Business $business;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $category = Category::factory()->create();
        $this->business = Business::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $category->id,
        ]);
        $token = $this->user->createToken('test-token')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $token];
    }

    public function test_user_can_create_product(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/products', [
                'name' => 'Ayam Geprek',
                'price' => 15000,
                'stock' => 50,
                'description' => 'Ayam geprek pedas nampol',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'product' => ['id', 'name', 'price', 'stock', 'description'],
            ])
            ->assertJson([
                'message' => 'Produk berhasil ditambahkan',
                'product' => ['name' => 'Ayam Geprek', 'price' => 15000, 'stock' => 50],
            ]);

        $this->assertDatabaseHas('products', [
            'business_id' => $this->business->id,
            'name' => 'Ayam Geprek',
        ]);
    }

    public function test_create_product_requires_name_and_price(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_price_must_be_non_negative(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/products', [
                'name' => 'Ayam Geprek',
                'price' => -100,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    public function test_user_can_list_products(): void
    {
        Product::factory()->count(3)->create([
            'business_id' => $this->business->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'products' => [],
            ]);

        $this->assertCount(3, $response->json('products'));
    }

    public function test_user_can_view_product(): void
    {
        $product = Product::factory()->create([
            'business_id' => $this->business->id,
            'name' => 'Nasi Goreng',
            'price' => 20000,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson([
                'product' => ['name' => 'Nasi Goreng', 'price' => 20000],
            ]);
    }

    public function test_user_cannot_view_another_users_product(): void
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create();
        $otherBusiness = Business::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
        ]);
        $otherProduct = Product::factory()->create([
            'business_id' => $otherBusiness->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson("/api/products/{$otherProduct->id}");

        $response->assertStatus(404);
    }

    public function test_user_can_update_their_product(): void
    {
        $product = Product::factory()->create([
            'business_id' => $this->business->id,
            'name' => 'Mie Ayam',
            'price' => 10000,
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/products/{$product->id}", [
                'price' => 12000,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Produk berhasil diperbarui',
                'product' => ['name' => 'Mie Ayam', 'price' => 12000],
            ]);
    }

    public function test_user_cannot_update_another_users_product(): void
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create();
        $otherBusiness = Business::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
        ]);
        $otherProduct = Product::factory()->create([
            'business_id' => $otherBusiness->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson("/api/products/{$otherProduct->id}", [
                'name' => 'Hacked',
            ]);

        $response->assertStatus(404);
    }

    public function test_user_can_delete_their_product(): void
    {
        $product = Product::factory()->create([
            'business_id' => $this->business->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Produk berhasil dihapus']);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_user_cannot_delete_another_users_product(): void
    {
        $otherUser = User::factory()->create();
        $otherCategory = Category::factory()->create();
        $otherBusiness = Business::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $otherCategory->id,
        ]);
        $otherProduct = Product::factory()->create([
            'business_id' => $otherBusiness->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson("/api/products/{$otherProduct->id}");

        $response->assertStatus(404);
    }

    public function test_products_require_auth(): void
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);

        $response = $this->postJson('/api/products', ['name' => 'Test', 'price' => 1000]);
        $response->assertStatus(401);
    }

    public function test_create_product_without_business_fails(): void
    {
        $userWithoutBusiness = User::factory()->create();
        $token = $userWithoutBusiness->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/products', [
            'name' => 'Test',
            'price' => 1000,
        ]);

        $response->assertStatus(404);
    }
}
