@extends('layouts.app')

@section('title', __('app.edit_transaction.page_title'))
@section('page-title', __('app.edit_transaction.page_title'))
@section('page-subtitle', __('app.edit_transaction.page_subtitle'))

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">

        <form method="POST" action="{{ route('transactions.update', $transaction) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Type selector --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">{{ __('app.create_transaction.type_label') }}</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="expense" class="sr-only peer"
                               {{ old('type', $transaction->type) === 'expense' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-700
                                    peer-checked:border-red-500 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-red-600 dark:peer-checked:text-red-400
                                    text-slate-500 dark:text-slate-400 hover:border-slate-300 text-sm font-medium transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            {{ __('app.transactions.expense_label') }}
                        </div>
                    </label>
                    <label class="relative cursor-pointer">
                        <input type="radio" name="type" value="income" class="sr-only peer"
                               {{ old('type', $transaction->type) === 'income' ? 'checked' : '' }}>
                        <div class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-700
                                    peer-checked:border-green-500 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 peer-checked:text-green-600 dark:peer-checked:text-green-400
                                    text-slate-500 dark:text-slate-400 hover:border-slate-300 text-sm font-medium transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            {{ __('app.transactions.income_label') }}
                        </div>
                    </label>
                </div>
                @error('type')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Amount --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.create_transaction.amount_label') }}</label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 dark:text-slate-400 text-sm font-medium">R$</span>
                    <input type="number" name="amount" value="{{ old('amount', $transaction->amount) }}" step="0.01" min="0.01" required
                           class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                @error('amount')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Category --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.create_transaction.category_label') }}</label>
                <select name="category_id" required
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ old('category_id', $transaction->category_id) == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.create_transaction.description_label') }}</label>
                <input type="text" name="description" value="{{ old('description', $transaction->description) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                @error('description')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Date --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.create_transaction.date_label') }}</label>
                <input type="date" name="date" value="{{ old('date', $transaction->date->format('Y-m-d')) }}" required
                       max="{{ now()->format('Y-m-d') }}"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                @error('date')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors">
                    {{ __('app.edit_transaction.update_btn') }}
                </button>
                <a href="{{ route('transactions.index') }}"
                   class="px-6 py-2.5 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium rounded-lg text-sm transition-colors">
                    {{ __('app.common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
