<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FinancialService
{
    public function __construct(private readonly int $userId) {}

    // ── Dashboard Summaries ────────────────────────────────────────────────────

    public function currentMonthExpenses(): float
    {
        return (float) Transaction::ofUser($this->userId)
            ->expenses()
            ->currentMonth()
            ->sum('amount');
    }

    public function currentMonthIncomes(): float
    {
        return (float) Transaction::ofUser($this->userId)
            ->incomes()
            ->currentMonth()
            ->sum('amount');
    }

    public function currentMonthBalance(): float
    {
        return $this->currentMonthIncomes() - $this->currentMonthExpenses();
    }

    public function totalBalance(): float
    {
        $income  = (float) Transaction::ofUser($this->userId)->incomes()->sum('amount');
        $expense = (float) Transaction::ofUser($this->userId)->expenses()->sum('amount');

        return $income - $expense;
    }

    // ── Chart Data ─────────────────────────────────────────────────────────────

    /**
     * Expenses grouped by category for the current month.
     * Returns: [['name' => 'Lazer', 'color' => '#8B5CF6', 'total' => 150.00], ...]
     */
    public function expensesByCategory(?string $month = null, ?string $year = null): Collection
    {
        $month = $month ?? now()->month;
        $year  = $year  ?? now()->year;

        return Transaction::ofUser($this->userId)
            ->expenses()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->join('categories', 'categories.id', '=', 'transactions.category_id')
            ->select(
                'categories.name',
                'categories.color',
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderByDesc('total')
            ->get();
    }

    /**
     * Monthly evolution for the last N months.
     * Returns: [['month' => '2024-01', 'expenses' => 1500, 'incomes' => 5000], ...]
     */
    public function monthlyEvolution(int $months = 6): Collection
    {
        $results = collect();

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);

            $expenses = (float) Transaction::ofUser($this->userId)
                ->expenses()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $incomes = (float) Transaction::ofUser($this->userId)
                ->incomes()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $results->push([
                'month'    => $date->format('M/Y'),
                'expenses' => $expenses,
                'incomes'  => $incomes,
                'balance'  => $incomes - $expenses,
            ]);
        }

        return $results;
    }

    /**
     * Top category with highest expense this month.
     */
    public function topExpenseCategory(): ?array
    {
        $top = $this->expensesByCategory()->first();

        return $top ? $top->toArray() : null;
    }

    // ── AI Data ────────────────────────────────────────────────────────────────

    /**
     * Structured data for AI analysis.
     */
    public function buildAiPayload(): array
    {
        $evolution = $this->monthlyEvolution(6);
        $byCategory = $this->expensesByCategory();

        return [
            'period' => 'últimos 6 meses',
            'monthly_summary' => $evolution->toArray(),
            'current_month' => [
                'expenses'   => $this->currentMonthExpenses(),
                'incomes'    => $this->currentMonthIncomes(),
                'balance'    => $this->currentMonthBalance(),
            ],
            'total_balance'  => $this->totalBalance(),
            'expenses_by_category' => $byCategory->toArray(),
            'top_expense_category' => $this->topExpenseCategory(),
            'recent_transactions' => Transaction::ofUser($this->userId)
                ->with('category:id,name')
                ->orderByDesc('date')
                ->limit(20)
                ->get(['type', 'amount', 'description', 'date', 'category_id'])
                ->map(fn ($t) => [
                    'type'        => $t->typeLabel(),
                    'amount'      => (float) $t->amount,
                    'description' => $t->description,
                    'date'        => $t->date->format('d/m/Y'),
                    'category'    => $t->category?->name,
                ])
                ->toArray(),
        ];
    }

    // ── Pagination ─────────────────────────────────────────────────────────────

    public function paginatedTransactions(array $filters = [], int $perPage = 15)
    {
        $query = Transaction::ofUser($this->userId)
            ->with('category:id,name,color')
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        if (!empty($filters['month'])) {
            $query->whereMonth('date', $filters['month']);
        }

        if (!empty($filters['year'])) {
            $query->whereYear('date', $filters['year']);
        }

        if (!empty($filters['search'])) {
            $query->where('description', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($perPage)->withQueryString();
    }
}
