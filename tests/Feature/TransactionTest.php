<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->category = Category::create(['name' => 'Lazer', 'color' => '#8B5CF6', 'icon' => 'tag']);
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('transactions.index'))->assertRedirect(route('login'));
    }

    public function test_index_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertOk()
            ->assertViewIs('transactions.index');
    }

    public function test_index_shows_only_own_transactions(): void
    {
        $otherUser = User::factory()->create();

        $this->tx(['description' => 'Minha transação']);
        Transaction::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 50,
            'description' => 'Transação alheia',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.index'))
            ->assertSee('Minha transação')
            ->assertDontSee('Transação alheia');
    }

    public function test_index_filter_by_type_expense(): void
    {
        $this->tx(['type' => 'expense', 'description' => 'Gasto aqui']);
        $this->tx(['type' => 'income',  'description' => 'Lucro aqui']);

        $this->actingAs($this->user)
            ->get(route('transactions.index', ['type' => 'expense']))
            ->assertSee('Gasto aqui')
            ->assertDontSee('Lucro aqui');
    }

    public function test_index_filter_by_search(): void
    {
        $this->tx(['description' => 'Supermercado']);
        $this->tx(['description' => 'Academia']);

        $this->actingAs($this->user)
            ->get(route('transactions.index', ['search' => 'Supermercado']))
            ->assertSee('Supermercado')
            ->assertDontSee('Academia');
    }

    // ── Create ────────────────────────────────────────────────────────────────

    public function test_create_page_loads(): void
    {
        $this->actingAs($this->user)
            ->get(route('transactions.create'))
            ->assertOk()
            ->assertViewIs('transactions.create');
    }

    // ── Store ─────────────────────────────────────────────────────────────────

    public function test_user_can_store_expense_transaction(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'expense',
                'amount' => 150.00,
                'category_id' => $this->category->id,
                'description' => 'Supermercado',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'type' => 'expense',
            'description' => 'Supermercado',
        ]);
    }

    public function test_user_can_store_income_transaction(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'income',
                'amount' => 5000.00,
                'category_id' => $this->category->id,
                'description' => 'Salário',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', ['type' => 'income', 'description' => 'Salário']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [])
            ->assertSessionHasErrors(['type', 'amount', 'category_id', 'description', 'date']);
    }

    public function test_store_rejects_invalid_type(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'invalid',
                'amount' => 100,
                'category_id' => $this->category->id,
                'description' => 'Test',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('type');
    }

    public function test_store_rejects_zero_amount(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'expense',
                'amount' => 0,
                'category_id' => $this->category->id,
                'description' => 'Test',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_store_rejects_negative_amount(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'expense',
                'amount' => -100,
                'category_id' => $this->category->id,
                'description' => 'Test',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('amount');
    }

    public function test_store_rejects_future_date(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'expense',
                'amount' => 100,
                'category_id' => $this->category->id,
                'description' => 'Test',
                'date' => now()->addDay()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('date');
    }

    public function test_store_rejects_nonexistent_category(): void
    {
        $this->actingAs($this->user)
            ->post(route('transactions.store'), [
                'type' => 'expense',
                'amount' => 100,
                'category_id' => 99999,
                'description' => 'Test',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertSessionHasErrors('category_id');
    }

    // ── Edit / Update ──────────────────────────────────────────────────────────

    public function test_edit_page_loads_with_transaction_data(): void
    {
        $t = $this->tx(['description' => 'Original']);

        $this->actingAs($this->user)
            ->get(route('transactions.edit', $t))
            ->assertOk()
            ->assertSee('Original');
    }

    public function test_user_can_update_own_transaction(): void
    {
        $t = $this->tx(['description' => 'Antes']);

        $this->actingAs($this->user)
            ->put(route('transactions.update', $t), [
                'type' => 'expense',
                'amount' => 200,
                'category_id' => $this->category->id,
                'description' => 'Depois',
                'date' => now()->format('Y-m-d'),
            ])
            ->assertRedirect(route('transactions.index'));

        $this->assertDatabaseHas('transactions', ['id' => $t->id, 'description' => 'Depois']);
    }

    public function test_user_cannot_edit_another_users_transaction(): void
    {
        $otherUser = User::factory()->create();
        $t = Transaction::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 100,
            'description' => 'Alheia',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->get(route('transactions.edit', $t))
            ->assertForbidden();
    }

    // ── Destroy ───────────────────────────────────────────────────────────────

    public function test_user_can_delete_own_transaction(): void
    {
        $t = $this->tx([]);

        $this->actingAs($this->user)
            ->delete(route('transactions.destroy', $t))
            ->assertRedirect(route('transactions.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('transactions', ['id' => $t->id]);
    }

    public function test_user_cannot_delete_another_users_transaction(): void
    {
        $otherUser = User::factory()->create();
        $t = Transaction::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 100,
            'description' => 'Alheia',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->delete(route('transactions.destroy', $t))
            ->assertForbidden();

        $this->assertDatabaseHas('transactions', ['id' => $t->id]);
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function tx(array $attrs): Transaction
    {
        return Transaction::create(array_merge([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 100.00,
            'description' => 'Teste',
            'date' => now()->format('Y-m-d'),
        ], $attrs));
    }
}
