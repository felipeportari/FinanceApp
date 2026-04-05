@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Visão geral financeira de ' . now()->translatedFormat('F Y'))

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">

    @php
        $cards = [
            [
                'label'  => 'Gastos no Mês',
                'value'  => $totalExpenses,
                'icon'   => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
                'color'  => 'text-red-600 dark:text-red-400',
                'bg'     => 'bg-red-50 dark:bg-red-900/20',
                'prefix' => '-',
            ],
            [
                'label'  => 'Lucros no Mês',
                'value'  => $totalIncomes,
                'icon'   => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                'color'  => 'text-green-600 dark:text-green-400',
                'bg'     => 'bg-green-50 dark:bg-green-900/20',
                'prefix' => '+',
            ],
            [
                'label'  => 'Saldo do Mês',
                'value'  => $monthBalance,
                'icon'   => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
                'color'  => $monthBalance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400',
                'bg'     => $monthBalance >= 0 ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-red-50 dark:bg-red-900/20',
                'prefix' => $monthBalance >= 0 ? '+' : '',
            ],
            [
                'label'  => 'Saldo Total',
                'value'  => $totalBalance,
                'icon'   => 'M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3',
                'color'  => $totalBalance >= 0 ? 'text-slate-700 dark:text-slate-200' : 'text-red-600 dark:text-red-400',
                'bg'     => 'bg-slate-100 dark:bg-slate-800',
                'prefix' => $totalBalance >= 0 ? '' : '',
            ],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 flex items-start gap-4">
            <div class="w-11 h-11 rounded-xl {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-0.5">{{ $card['label'] }}</p>
                <p class="text-xl font-bold {{ $card['color'] }}">
                    {{ $card['prefix'] }}R$ {{ number_format(abs($card['value']), 2, ',', '.') }}
                </p>
            </div>
        </div>
    @endforeach
</div>

{{-- Charts Row --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-8">

    {{-- Monthly Evolution Chart (2/3 width) --}}
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-slate-800 dark:text-white">Evolução Mensal</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Gastos vs Lucros — últimos 6 meses</p>
            </div>
        </div>
        <div class="relative h-64">
            <canvas id="evolutionChart"></canvas>
        </div>
    </div>

    {{-- Expenses by Category (1/3 width) --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <div class="mb-5">
            <h3 class="font-semibold text-slate-800 dark:text-white">Gastos por Categoria</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Mês atual</p>
        </div>
        @if($expensesByCategory->isEmpty())
            <div class="h-64 flex items-center justify-center text-sm text-slate-400">
                Nenhum gasto registrado neste mês.
            </div>
        @else
            <div class="relative h-48 mb-4">
                <canvas id="categoryChart"></canvas>
            </div>
            {{-- Legend --}}
            <div class="space-y-1.5">
                @foreach($expensesByCategory->take(5) as $cat)
                    <div class="flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $cat->color }}"></span>
                            <span class="text-slate-600 dark:text-slate-300">{{ $cat->name }}</span>
                        </div>
                        <span class="font-medium text-slate-800 dark:text-white">R$ {{ number_format($cat->total, 2, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Bottom Row: Top Category + Recent Transactions --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- Top Impact Category --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <h3 class="font-semibold text-slate-800 dark:text-white mb-4">Maior Impacto</h3>
        @if($topCategory)
            <div class="flex flex-col items-center text-center py-4">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-3" style="background-color: {{ $topCategory['color'] }}22; border: 2px solid {{ $topCategory['color'] }}44">
                    <span class="text-2xl" style="color: {{ $topCategory['color'] }}">$</span>
                </div>
                <p class="font-bold text-slate-800 dark:text-white text-lg">{{ $topCategory['name'] }}</p>
                <p class="text-2xl font-bold mt-1" style="color: {{ $topCategory['color'] }}">
                    R$ {{ number_format($topCategory['total'], 2, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">categoria com mais gastos</p>
            </div>
        @else
            <p class="text-sm text-slate-400 text-center py-8">Nenhum gasto registrado.</p>
        @endif
    </div>

    {{-- Recent Transactions --}}
    <div class="xl:col-span-2 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800 dark:text-white">Últimas Transações</h3>
            <a href="{{ route('transactions.index') }}" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">Ver todas</a>
        </div>

        @php
            $recent = \App\Models\Transaction::ofUser(auth()->id())
                ->with('category:id,name,color')
                ->orderByDesc('date')->orderByDesc('id')
                ->limit(6)->get();
        @endphp

        @if($recent->isEmpty())
            <p class="text-sm text-slate-400 text-center py-8">Nenhuma transação registrada.</p>
        @else
            <div class="space-y-3">
                @foreach($recent as $t)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                             style="background-color: {{ $t->category?->color ?? '#64748B' }}22">
                            <span class="text-xs font-bold" style="color: {{ $t->category?->color ?? '#64748B' }}">
                                {{ strtoupper(substr($t->category?->name ?? 'O', 0, 1)) }}
                            </span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-white truncate">{{ $t->description }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $t->category?->name }} · {{ $t->date->format('d/m/Y') }}
                            </p>
                        </div>
                        <p class="text-sm font-semibold flex-shrink-0 {{ $t->isExpense() ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                            {{ $t->isExpense() ? '-' : '+' }}R$ {{ number_format($t->amount, 2, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script>
const isDark = document.documentElement.classList.contains('dark');
const gridColor  = isDark ? 'rgba(148,163,184,0.1)' : 'rgba(100,116,139,0.1)';
const textColor  = isDark ? '#94A3B8' : '#64748B';

// ── Evolution Chart ──────────────────────────────────────────────────────────
const evolutionData = @json($monthlyEvolution);

new Chart(document.getElementById('evolutionChart'), {
    type: 'bar',
    data: {
        labels: evolutionData.map(d => d.month),
        datasets: [
            {
                label: 'Gastos',
                data: evolutionData.map(d => d.expenses),
                backgroundColor: 'rgba(239,68,68,0.7)',
                borderRadius: 6,
            },
            {
                label: 'Lucros',
                data: evolutionData.map(d => d.incomes),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 6,
            },
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { labels: { color: textColor, boxWidth: 12, padding: 16 } },
            tooltip: {
                callbacks: {
                    label: ctx => ` R$ ${ctx.parsed.y.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`
                }
            }
        },
        scales: {
            x: { grid: { color: gridColor }, ticks: { color: textColor } },
            y: { grid: { color: gridColor }, ticks: { color: textColor,
                callback: v => 'R$ ' + v.toLocaleString('pt-BR') } }
        }
    }
});

// ── Category Doughnut ─────────────────────────────────────────────────────────
@if($expensesByCategory->isNotEmpty())
const catData = @json($expensesByCategory);

new Chart(document.getElementById('categoryChart'), {
    type: 'doughnut',
    data: {
        labels: catData.map(d => d.name),
        datasets: [{
            data:            catData.map(d => d.total),
            backgroundColor: catData.map(d => d.color),
            borderWidth:     2,
            borderColor:     isDark ? '#0F172A' : '#FFFFFF',
            hoverOffset:     4,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ` R$ ${ctx.parsed.toLocaleString('pt-BR', {minimumFractionDigits: 2})}`
                }
            }
        },
        cutout: '72%',
    }
});
@endif
</script>
@endpush
