<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
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

    public function test_dashboard_requires_authentication(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_dashboard_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewIs('dashboard.index');
    }

    public function test_dashboard_passes_required_view_variables(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertViewHasAll([
                'totalExpenses',
                'totalIncomes',
                'monthBalance',
                'totalBalance',
                'monthlyEvolution',
                'expensesByCategory',
                'topCategory',
            ]);
    }

    public function test_dashboard_shows_correct_monthly_expenses(): void
    {
        Transaction::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 750.00,
            'description' => 'Aluguel',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertViewHas('totalExpenses', 750.0);
    }

    public function test_dashboard_shows_correct_monthly_incomes(): void
    {
        Transaction::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'income',
            'amount' => 5000.00,
            'description' => 'Salário',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertViewHas('totalIncomes', 5000.0);
    }

    public function test_dashboard_shows_correct_month_balance(): void
    {
        Transaction::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'income',
            'amount' => 5000,
            'description' => 'Salário',
            'date' => now()->format('Y-m-d'),
        ]);

        Transaction::create([
            'user_id' => $this->user->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 1500,
            'description' => 'Gastos',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertViewHas('monthBalance', 3500.0);
    }

    public function test_dashboard_does_not_show_other_users_data(): void
    {
        $otherUser = User::factory()->create();

        Transaction::create([
            'user_id' => $otherUser->id,
            'category_id' => $this->category->id,
            'type' => 'expense',
            'amount' => 9999,
            'description' => 'Não deve aparecer',
            'date' => now()->format('Y-m-d'),
        ]);

        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertViewHas('totalExpenses', 0.0);
    }

    public function test_dashboard_top_category_is_null_with_no_expenses(): void
    {
        $this->actingAs($this->user)
            ->get(route('dashboard'))
            ->assertViewHas('topCategory', null);
    }
}
