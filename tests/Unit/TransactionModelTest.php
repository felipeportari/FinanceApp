<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = User::factory()->create();
        $this->category = Category::create([
            'name'  => 'Teste',
            'color' => '#3B82F6',
            'icon'  => 'tag',
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function test_is_expense_returns_true_for_expense(): void
    {
        $t = $this->makeTransaction(['type' => 'expense']);
        $this->assertTrue($t->isExpense());
        $this->assertFalse($t->isIncome());
    }

    public function test_is_income_returns_true_for_income(): void
    {
        $t = $this->makeTransaction(['type' => 'income']);
        $this->assertTrue($t->isIncome());
        $this->assertFalse($t->isExpense());
    }

    public function test_type_label_returns_portuguese(): void
    {
        $this->assertSame('Gasto',  $this->makeTransaction(['type' => 'expense'])->typeLabel());
        $this->assertSame('Lucro',  $this->makeTransaction(['type' => 'income'])->typeLabel());
    }

    public function test_formatted_amount_uses_brl_format(): void
    {
        $t = $this->makeTransaction(['amount' => 1500.50]);
        $this->assertSame('R$ 1.500,50', $t->formattedAmount());
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function test_scope_expenses_filters_only_expenses(): void
    {
        $this->makeTransaction(['type' => 'expense']);
        $this->makeTransaction(['type' => 'expense']);
        $this->makeTransaction(['type' => 'income']);

        $this->assertCount(2, Transaction::expenses()->get());
    }

    public function test_scope_incomes_filters_only_incomes(): void
    {
        $this->makeTransaction(['type' => 'income']);
        $this->makeTransaction(['type' => 'expense']);

        $this->assertCount(1, Transaction::incomes()->get());
    }

    public function test_scope_current_month_filters_current_month_only(): void
    {
        $this->makeTransaction(['date' => now()->format('Y-m-d')]);
        $this->makeTransaction(['date' => now()->subMonths(2)->format('Y-m-d')]);
        $this->makeTransaction(['date' => now()->subMonths(3)->format('Y-m-d')]);

        $this->assertCount(1, Transaction::currentMonth()->get());
    }

    public function test_scope_of_user_filters_by_user(): void
    {
        $otherUser = User::factory()->create();

        $this->makeTransaction([]);
        Transaction::create([
            'user_id'     => $otherUser->id,
            'category_id' => $this->category->id,
            'type'        => 'expense',
            'amount'      => 50,
            'description' => 'outro',
            'date'        => now()->format('Y-m-d'),
        ]);

        $this->assertCount(1, Transaction::ofUser($this->user->id)->get());
    }

    // ── Relations ──────────────────────────────────────────────────────────────

    public function test_transaction_belongs_to_user(): void
    {
        $t = $this->makeTransaction([]);
        $this->assertInstanceOf(User::class, $t->user);
        $this->assertSame($this->user->id, $t->user->id);
    }

    public function test_transaction_belongs_to_category(): void
    {
        $t = $this->makeTransaction([]);
        $this->assertInstanceOf(Category::class, $t->category);
        $this->assertSame($this->category->id, $t->category->id);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeTransaction(array $attrs): Transaction
    {
        return Transaction::create(array_merge([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'type'        => 'expense',
            'amount'      => 100.00,
            'description' => 'Teste',
            'date'        => now()->format('Y-m-d'),
        ], $attrs));
    }
}
