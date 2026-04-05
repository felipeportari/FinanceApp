<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'type',
        'amount',
        'description',
        'date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'date'   => 'date',
        ];
    }

    // ── Relations ──────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeExpenses(Builder $query): Builder
    {
        return $query->where('type', 'expense');
    }

    public function scopeIncomes(Builder $query): Builder
    {
        return $query->where('type', 'income');
    }

    public function scopeCurrentMonth(Builder $query): Builder
    {
        return $query->whereYear('date', now()->year)
                     ->whereMonth('date', now()->month);
    }

    public function scopeOfUser(Builder $query, int $userId): Builder
    {
        return $query->where('transactions.user_id', $userId);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isExpense(): bool
    {
        return $this->type === 'expense';
    }

    public function isIncome(): bool
    {
        return $this->type === 'income';
    }

    public function typeLabel(): string
    {
        return $this->type === 'expense' ? 'Gasto' : 'Lucro';
    }

    public function formattedAmount(): string
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }
}
