@extends('layouts.app')

@section('title', 'Categorias')
@section('page-title', 'Categorias')
@section('page-subtitle', 'Gerencie as categorias de transações')

@section('content')

<div class="flex justify-end mb-5">
    <a href="{{ route('categories.create') }}"
       class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nova Categoria
    </a>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    @forelse($categories as $cat)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 flex flex-col gap-3">

            <div class="flex items-start justify-between">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                     style="background-color: {{ $cat->color }}22; border: 1.5px solid {{ $cat->color }}44">
                    <span class="text-base font-bold" style="color: {{ $cat->color }}">
                        {{ strtoupper(substr($cat->name, 0, 1)) }}
                    </span>
                </div>
                @if(!$cat->is_default)
                    <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                          onsubmit="return confirm('Remover categoria {{ $cat->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-slate-300 dark:text-slate-600 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </form>
                @else
                    <span class="text-xs font-medium px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-500 dark:text-slate-400 rounded-full">
                        padrão
                    </span>
                @endif
            </div>

            <div>
                <p class="font-semibold text-slate-800 dark:text-white">{{ $cat->name }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $cat->transactions_count }} {{ Str::plural('transação', $cat->transactions_count) }}
                </p>
            </div>

            {{-- Color indicator --}}
            <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $cat->color }}"></span>
                {{ $cat->color }}
            </div>
        </div>
    @empty
        <div class="col-span-full py-12 text-center text-slate-400">
            Nenhuma categoria encontrada.
        </div>
    @endforelse
</div>

@endsection
