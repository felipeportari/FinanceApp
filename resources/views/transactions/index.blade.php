@extends('layouts.app')

@section('title', __('app.transactions.page_title'))
@section('page-title', __('app.transactions.page_title'))
@section('page-subtitle', __('app.transactions.page_subtitle'))

@section('content')

{{-- Filters --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-4 mb-5">
    <form method="GET" action="{{ route('transactions.index') }}" class="flex flex-wrap gap-3">

        {{-- Search --}}
        <div class="flex-1 min-w-48">
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="{{ __('app.transactions.search_placeholder') }}"
                   class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>

        {{-- Type --}}
        <select name="type"
                class="px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">{{ __('app.transactions.all_types') }}</option>
            <option value="expense" {{ ($filters['type'] ?? '') === 'expense' ? 'selected' : '' }}>{{ __('app.transactions.expenses') }}</option>
            <option value="income"  {{ ($filters['type'] ?? '') === 'income'  ? 'selected' : '' }}>{{ __('app.transactions.income') }}</option>
        </select>

        {{-- Category --}}
        <select name="category_id"
                class="px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">{{ __('app.transactions.all_categories') }}</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ ($filters['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>

        {{-- Month --}}
        <select name="month"
                class="px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">{{ __('app.transactions.all_months') }}</option>
            @foreach(range(1, 12) as $m)
                <option value="{{ $m }}" {{ ($filters['month'] ?? '') == $m ? 'selected' : '' }}>
                    {{ \Carbon\Carbon::create()->month($m)->isoFormat('MMMM') }}
                </option>
            @endforeach
        </select>

        {{-- Year --}}
        <select name="year"
                class="px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">{{ __('app.transactions.all_years') }}</option>
            @foreach(range(now()->year, now()->year - 3) as $y)
                <option value="{{ $y }}" {{ ($filters['year'] ?? '') == $y ? 'selected' : '' }}>{{ $y }}</option>
            @endforeach
        </select>

        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg">
            {{ __('app.transactions.filter') }}
        </button>

        @if(array_filter($filters))
            <a href="{{ route('transactions.index') }}" class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:underline self-center">
                {{ __('app.transactions.clear') }}
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">

    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            <span class="font-semibold text-slate-800 dark:text-white">{{ $transactions->total() }}</span> {{ __('app.transactions.results_label') }}
        </p>
        <a href="{{ route('transactions.create') }}"
           class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            {{ __('app.transactions.new_transaction') }}
        </a>
    </div>

    @if($transactions->isEmpty())
        <div class="py-16 text-center">
            <svg class="w-12 h-12 text-slate-300 dark:text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-slate-500 dark:text-slate-400">{{ __('app.transactions.no_found') }}</p>
            <a href="{{ route('transactions.create') }}" class="mt-3 inline-block text-sm text-emerald-600 hover:underline">
                {{ __('app.transactions.add_first') }}
            </a>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.transactions.col_description') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.transactions.col_category') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.transactions.col_date') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.transactions.col_type') }}</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.transactions.col_amount') }}</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($transactions as $t)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-3.5 font-medium text-slate-800 dark:text-white">{{ $t->description }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full"
                                      style="background-color: {{ $t->category?->color ?? '#64748B' }}22; color: {{ $t->category?->color ?? '#64748B' }}">
                                    {{ $t->category?->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">{{ $t->date->format('d/m/Y') }}</td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full
                                    {{ $t->isExpense()
                                        ? 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400'
                                        : 'bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400' }}">
                                    {{ $t->typeLabel() }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right font-semibold
                                {{ $t->isExpense() ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $t->isExpense() ? '-' : '+' }}R$ {{ number_format($t->amount, 2, ',', '.') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('transactions.edit', $t) }}"
                                       class="text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('transactions.destroy', $t) }}"
                                          onsubmit="return confirm('{{ __('app.transactions.remove_confirm') }}')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $transactions->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
