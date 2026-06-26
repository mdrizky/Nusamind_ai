<?php

namespace Tests\Feature\Api;

use App\Models\Business;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BusinessTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::factory()->create(['name' => 'Makanan & Minuman']);
        $token = $this->user->createToken('test-token')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $token];
    }

    public function test_user_can_create_business(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', [
                'business_name' => 'Warung Sari Rasa',
                'category_id' => $this->category->id,
                'city' => 'Pekanbaru',
                'description' => 'Warung makan rumahan',
            ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'business' => ['id', 'business_name', 'city', 'category'],
            ])
            ->assertJson([
                'message' => 'Profil usaha berhasil dibuat',
                'business' => ['business_name' => 'Warung Sari Rasa', 'city' => 'Pekanbaru'],
            ]);

        $this->assertDatabaseHas('businesses', [
            'user_id' => $this->user->id,
            'business_name' => 'Warung Sari Rasa',
        ]);
    }

    public function test_create_business_requires_name_category_city(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['business_name', 'category_id', 'city']);
    }

    public function test_create_business_requires_valid_category(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/business', [
                'business_name' => 'Test',
                'category_id' => 9999,
                'city' => 'Test',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['category_id']);
    }

    public function test_user_can_view_their_business(): void
    {
        $business = Business::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/business/me');

        $response->assertStatus(200)
            ->assertJson([
                'business' => ['id' => $business->id, 'business_name' => $business->business_name],
            ]);
    }

    public function test_user_without_business_gets_null(): void
    {
        $response = $this->withHeaders($this->headers)
            ->getJson('/api/business/me');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profil usaha belum dibuat',
                'business' => null,
            ]);
    }

    public function test_user_can_update_business(): void
    {
        $business = Business::factory()->create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/business/me', [
                'business_name' => 'Warung Baru',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Profil usaha berhasil diperbarui',
                'business' => ['business_name' => 'Warung Baru'],
            ]);
    }

    public function test_update_business_without_owning_one_fails(): void
    {
        $response = $this->withHeaders($this->headers)
            ->putJson('/api/business/me', [
                'business_name' => 'Test',
            ]);

        $response->assertStatus(404);
    }

    public function test_user_cannot_access_another_users_business(): void
    {
        $otherUser = User::factory()->create();
        $otherBusiness = Business::factory()->create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/business/me');

        $response->assertStatus(200)
            ->assertJson(['business' => null]);
    }

    public function test_create_business_requires_auth(): void
    {
        $response = $this->postJson('/api/business', [
            'business_name' => 'Unauth',
            'category_id' => 1,
            'city' => 'Test',
        ]);

        $response->assertStatus(401);
    }
}
