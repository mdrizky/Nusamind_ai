<?php

namespace Tests\Feature\Api;

use App\Models\Business;
use App\Models\Category;
use App\Models\ContentGeneration;
use App\Models\ContentReport;
use App\Models\Notification;
use App\Models\Product;
use App\Models\User;
use App\Services\AiFinanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiFinanceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $token = $this->user->createToken('test-token')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $token, 'Accept' => 'application/json'];

        // Seed category and create business so product testing works
        Category::factory()->create(['id' => 1]);
        Business::factory()->create(['user_id' => $this->user->id, 'category_id' => 1]);
    }

    public function test_extract_requires_input_text(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/ai/finance/extract', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['input_text']);
    }

    public function test_extract_requires_minimum_length(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/ai/finance/extract', ['input_text' => 'ab']);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['input_text']);
    }

    public function test_extract_returns_503_when_ai_fails(): void
    {
        $this->mock(AiFinanceService::class, function ($mock) {
            $mock->shouldReceive('extractTransactions')
                ->once()
                ->andThrow(new \Exception('Maaf, Nusamind sedang sibuk. Coba lagi ya!'));
        });

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/ai/finance/extract', ['input_text' => 'Hari ini jualan 5 porsi ayam']);

        $response->assertStatus(503)
            ->assertJson(['message' => 'Maaf, Nusamind sedang sibuk. Coba lagi ya!']);
    }

    public function test_extract_parses_successfully(): void
    {
        $this->mock(AiFinanceService::class, function ($mock) {
            $mock->shouldReceive('extractTransactions')
                ->once()
                ->andReturn([
                    ['type' => 'pemasukan', 'item_name' => 'ayam geprek', 'quantity' => 5, 'amount' => 75000],
                    ['type' => 'pengeluaran', 'item_name' => 'minyak goreng', 'quantity' => 1, 'amount' => 20000],
                ]);
        });

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/ai/finance/extract', ['input_text' => 'Hari ini laku 5 porsi ayam geprek total 75 ribu, terus beli minyak goreng 20 ribu']);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'transactions' => [['type', 'item_name', 'quantity', 'amount']],
                'note',
            ])
            ->assertJson([
                'transactions' => [
                    ['type' => 'pemasukan', 'item_name' => 'ayam geprek', 'quantity' => 5, 'amount' => 75000],
                    ['type' => 'pengeluaran', 'item_name' => 'minyak goreng', 'quantity' => 1, 'amount' => 20000],
                ],
                'note' => 'Silakan konfirmasi sebelum disimpan',
            ]);
    }

    public function test_extract_enforces_rate_limit(): void
    {
        // Seed 30 AiUsageLog entries for today to trigger the limit
        for ($i = 0; $i < 30; $i++) {
            \App\Models\AiUsageLog::create([
                'user_id' => $this->user->id,
                'feature' => 'finance',
                'status' => 'success',
                'created_at' => now(),
            ]);
        }

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/ai/finance/extract', ['input_text' => 'test transaction']);

        $response->assertStatus(429)
            ->assertJson(['message' => 'Kamu sudah mencapai batas pemakaian AI hari ini (30x). Besok lagi ya!']);
    }

    public function test_store_batch_transactions(): void
    {
        Product::factory()->create(['business_id' => 1, 'name' => 'Ayam Geprek', 'price' => 15000]);

        $response = $this->withHeaders($this->headers)
            ->postJson('/api/transactions', [
                'transactions' => [
                    ['type' => 'pemasukan', 'item_name' => 'Ayam Geprek', 'quantity' => 5, 'amount' => 75000,
                     'product_id' => 1, 'source' => 'ai_text', 'raw_input' => 'test input'],
                    ['type' => 'pengeluaran', 'item_name' => 'Minyak Goreng', 'quantity' => 1, 'amount' => 20000,
                     'source' => 'manual'],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Transaksi tersimpan', 'count' => 2]);

        $this->assertDatabaseCount('transactions', 2);
    }

    public function test_store_batch_validates_transactions(): void
    {
        $response = $this->withHeaders($this->headers)
            ->postJson('/api/transactions', [
                'transactions' => [
                    ['type' => 'invalid', 'item_name' => 'Test', 'amount' => -100, 'source' => 'unknown'],
                ],
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['transactions.0.type', 'transactions.0.amount', 'transactions.0.source']);
    }

    public function test_list_transactions_with_summary(): void
    {
        $this->withHeaders($this->headers)->postJson('/api/transactions', [
            'transactions' => [
                ['type' => 'pemasukan', 'item_name' => 'Ayam', 'quantity' => 2, 'amount' => 50000, 'source' => 'manual'],
                ['type' => 'pengeluaran', 'item_name' => 'Beras', 'quantity' => 1, 'amount' => 10000, 'source' => 'manual'],
            ],
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/transactions?filter=today');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'transactions',
                'summary' => ['total_income', 'total_expense', 'balance'],
            ])
            ->assertJson([
                'summary' => ['total_income' => 50000, 'total_expense' => 10000, 'balance' => 40000],
            ]);
    }

    public function test_filter_transactions_by_type(): void
    {
        $this->withHeaders($this->headers)->postJson('/api/transactions', [
            'transactions' => [
                ['type' => 'pemasukan', 'item_name' => 'Ayam', 'quantity' => 1, 'amount' => 25000, 'source' => 'manual'],
                ['type' => 'pengeluaran', 'item_name' => 'Minyak', 'quantity' => 1, 'amount' => 15000, 'source' => 'manual'],
            ],
        ]);

        $response = $this->withHeaders($this->headers)
            ->getJson('/api/transactions?type=pemasukan');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('transactions'));
        $this->assertEquals('pemasukan', $response->json('transactions.0.type'));
    }

    public function test_update_transaction(): void
    {
        $this->withHeaders($this->headers)->postJson('/api/transactions', [
            'transactions' => [['type' => 'pemasukan', 'item_name' => 'Test', 'amount' => 10000, 'source' => 'manual']],
        ]);

        $response = $this->withHeaders($this->headers)
            ->putJson('/api/transactions/1', ['amount' => 15000]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaksi berhasil diperbarui'])
            ->assertJson(['transaction' => ['amount' => 15000]]);
    }

    public function test_delete_transaction(): void
    {
        $this->withHeaders($this->headers)->postJson('/api/transactions', [
            'transactions' => [['type' => 'pemasukan', 'item_name' => 'Test', 'amount' => 10000, 'source' => 'manual']],
        ]);

        $response = $this->withHeaders($this->headers)
            ->deleteJson('/api/transactions/1');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaksi berhasil dihapus']);

        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_cannot_access_other_users_transaction(): void
    {
        $otherUser = User::factory()->create();
        Category::factory()->create(['id' => 99]);
        Business::factory()->create(['user_id' => $otherUser->id, 'category_id' => 99]);

        $otherUser->createToken('other-token')->plainTextToken;

        // Other user creates a transaction
        $this->actingAs($otherUser)
            ->postJson('/api/transactions', [
                'transactions' => [['type' => 'pemasukan', 'item_name' => 'Other', 'amount' => 10000, 'source' => 'manual']],
            ]);

        // First user tries to access it
        $response = $this->actingAs($this->user)
            ->getJson('/api/transactions/1');

        $response->assertStatus(404);
    }
}
