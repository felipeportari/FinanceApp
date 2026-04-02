<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Services\FinancialService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Category $category;
    private FinancialService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = User::factory()->create();
        $this->category = Category::create(['name' => 'Lazer', 'color' => '#8B5CF6', 'icon' => 'tag']);
        $this->service  = new FinancialService($this->user->id);
    }

    // ── Current month summaries ────────────────────────────────────────────────

    public function test_current_month_expenses_sums_only_current_month(): void
    {
        $this->tx(['type' => 'expense', 'amount' => 300, 'date' => now()]);
        $this->tx(['type' => 'expense', 'amount' => 200, 'date' => now()]);
        $this->tx(['type' => 'expense', 'amount' => 999, 'date' => now()->subMonth()]);

        $this->assertEquals(500.0, $this->service->currentMonthExpenses());
    }

    public function test_current_month_incomes_sums_only_current_month(): void
    {
        $this->tx(['type' => 'income', 'amount' => 5000, 'date' => now()]);
        $this->tx(['type' => 'income', 'amount' => 1000, 'date' => now()->subMonths(2)]);

        $this->assertEquals(5000.0, $this->service->currentMonthIncomes());
    }

    public function test_current_month_balance_is_income_minus_expenses(): void
    {
        $this->tx(['type' => 'income',  'amount' => 5000, 'date' => now()]);
        $this->tx(['type' => 'expense', 'amount' => 1500, 'date' => now()]);

        $this->assertEquals(3500.0, $this->service->currentMonthBalance());
    }

    public function test_current_month_balance_can_be_negative(): void
    {
        $this->tx(['type' => 'income',  'amount' => 1000, 'date' => now()]);
        $this->tx(['type' => 'expense', 'amount' => 3000, 'date' => now()]);

        $this->assertEquals(-2000.0, $this->service->currentMonthBalance());
    }

    public function test_total_balance_aggregates_all_time(): void
    {
        $this->tx(['type' => 'income',  'amount' => 5000, 'date' => now()]);
        $this->tx(['type' => 'income',  'amount' => 3000, 'date' => now()->subMonths(3)]);
        $this->tx(['type' => 'expense', 'amount' => 2000, 'date' => now()->subMonths(2)]);

        $this->assertEquals(6000.0, $this->service->totalBalance());
    }

    public function test_returns_zero_when_no_transactions(): void
    {
        $this->assertEquals(0.0, $this->service->currentMonthExpenses());
        $this->assertEquals(0.0, $this->service->currentMonthIncomes());
        $this->assertEquals(0.0, $this->service->currentMonthBalance());
        $this->assertEquals(0.0, $this->service->totalBalance());
    }

    // ── Expenses by category ───────────────────────────────────────────────────

    public function test_expenses_by_category_groups_and_sums_correctly(): void
    {
        $cat2 = Category::create(['name' => 'Transporte', 'color' => '#F59E0B', 'icon' => 'truck']);

        $this->tx(['type' => 'expense', 'amount' => 100, 'date' => now(), 'category_id' => $this->category->id]);
        $this->tx(['type' => 'expense', 'amount' => 200, 'date' => now(), 'category_id' => $this->category->id]);
        $this->tx(['type' => 'expense', 'amount' => 50,  'date' => now(), 'category_id' => $cat2->id]);

        $result = $this->service->expensesByCategory();

        $this->assertCount(2, $result);
        $this->assertEquals(300.0, (float) $result->firstWhere('name', 'Lazer')->total);
        $this->assertEquals(50.0,  (float) $result->firstWhere('name', 'Transporte')->total);
    }

    public function test_expenses_by_category_excludes_incomes(): void
    {
        $this->tx(['type' => 'income',  'amount' => 5000, 'date' => now()]);
        $this->tx(['type' => 'expense', 'amount' => 100,  'date' => now()]);

        $result = $this->service->expensesByCategory();
        $this->assertCount(1, $result);
        $this->assertEquals(100.0, (float) $result->first()->total);
    }

    public function test_expenses_by_category_is_ordered_by_total_desc(): void
    {
        $cat2 = Category::create(['name' => 'Transporte', 'color' => '#F59E0B', 'icon' => 'truck']);

        $this->tx(['type' => 'expense', 'amount' => 50,  'date' => now(), 'category_id' => $this->category->id]);
        $this->tx(['type' => 'expense', 'amount' => 500, 'date' => now(), 'category_id' => $cat2->id]);

        $result = $this->service->expensesByCategory();
        $this->assertEquals('Transporte', $result->first()->name);
    }

    // ── Top category ───────────────────────────────────────────────────────────

    public function test_top_expense_category_returns_highest(): void
    {
        $cat2 = Category::create(['name' => 'Remédios', 'color' => '#EF4444', 'icon' => 'heart']);

        $this->tx(['type' => 'expense', 'amount' => 100, 'date' => now(), 'category_id' => $this->category->id]);
        $this->tx(['type' => 'expense', 'amount' => 900, 'date' => now(), 'category_id' => $cat2->id]);

        $top = $this->service->topExpenseCategory();
        $this->assertSame('Remédios', $top['name']);
        $this->assertEquals(900.0, (float) $top['total']);
    }

    public function test_top_expense_category_returns_null_when_no_expenses(): void
    {
        $this->assertNull($this->service->topExpenseCategory());
    }

    // ── Monthly evolution ──────────────────────────────────────────────────────

    public function test_monthly_evolution_returns_correct_number_of_months(): void
    {
        $result = $this->service->monthlyEvolution(6);
        $this->assertCount(6, $result);
    }

    public function test_monthly_evolution_contains_required_keys(): void
    {
        $month = $this->service->monthlyEvolution(1)->first();
        $this->assertArrayHasKey('month',    $month);
        $this->assertArrayHasKey('expenses', $month);
        $this->assertArrayHasKey('incomes',  $month);
        $this->assertArrayHasKey('balance',  $month);
    }

    public function test_monthly_evolution_balance_is_income_minus_expense(): void
    {
        $this->tx(['type' => 'income',  'amount' => 5000, 'date' => now()]);
        $this->tx(['type' => 'expense', 'amount' => 1500, 'date' => now()]);

        $current = $this->service->monthlyEvolution(1)->first();
        $this->assertEquals(3500.0, $current['balance']);
    }

    // ── AI payload ─────────────────────────────────────────────────────────────

    public function test_build_ai_payload_contains_required_keys(): void
    {
        $payload = $this->service->buildAiPayload();

        $this->assertArrayHasKey('period',               $payload);
        $this->assertArrayHasKey('monthly_summary',      $payload);
        $this->assertArrayHasKey('current_month',        $payload);
        $this->assertArrayHasKey('total_balance',        $payload);
        $this->assertArrayHasKey('expenses_by_category', $payload);
        $this->assertArrayHasKey('recent_transactions',  $payload);
    }

    public function test_build_ai_payload_recent_transactions_max_20(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $this->tx(['type' => 'expense', 'amount' => 10, 'date' => now()]);
        }

        $payload = $this->service->buildAiPayload();
        $this->assertCount(20, $payload['recent_transactions']);
    }

    // ── Isolation between users ────────────────────────────────────────────────

    public function test_service_is_isolated_per_user(): void
    {
        $otherUser    = User::factory()->create();
        $otherService = new FinancialService($otherUser->id);

        $this->tx(['type' => 'expense', 'amount' => 999, 'date' => now()]);

        $this->assertEquals(999.0, $this->service->currentMonthExpenses());
        $this->assertEquals(0.0,   $otherService->currentMonthExpenses());
    }

    // ── Paginated transactions ─────────────────────────────────────────────────

    public function test_paginated_transactions_respects_type_filter(): void
    {
        $this->tx(['type' => 'expense']);
        $this->tx(['type' => 'expense']);
        $this->tx(['type' => 'income']);

        $result = $this->service->paginatedTransactions(['type' => 'expense']);
        $this->assertEquals(2, $result->total());
    }

    public function test_paginated_transactions_respects_search_filter(): void
    {
        $this->tx(['description' => 'Supermercado Pão de Açúcar']);
        $this->tx(['description' => 'Gasolina posto']);
        $this->tx(['description' => 'Supermercado Atacadão']);

        $result = $this->service->paginatedTransactions(['search' => 'Supermercado']);
        $this->assertEquals(2, $result->total());
    }

    // ── Helper ────────────────────────────────────────────────────────────────

    private function tx(array $attrs): Transaction
    {
        return Transaction::create(array_merge([
            'user_id'     => $this->user->id,
            'category_id' => $this->category->id,
            'type'        => 'expense',
            'amount'      => 100.00,
            'description' => 'Teste',
            'date'        => now()->format('Y-m-d'),
        ], array_merge($attrs, [
            'date' => isset($attrs['date'])
                ? (is_string($attrs['date']) ? $attrs['date'] : $attrs['date']->format('Y-m-d'))
                : now()->format('Y-m-d'),
        ])));
    }
}
