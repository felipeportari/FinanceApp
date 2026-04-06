<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'color',
        'icon',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
        ];
    }

    /**
     * Global scope: returns default categories (user_id = null)
     * plus the authenticated user's own categories.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('forUser', function (Builder $query) {
            if (auth()->check()) {
                $query->where(function (Builder $q) {
                    $q->whereNull('categories.user_id')
                        ->orWhere('categories.user_id', auth()->id());
                });
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
