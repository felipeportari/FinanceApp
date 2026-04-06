@extends('layouts.app')

@section('title', __('app.admin.dashboard'))
@section('page-title', __('app.admin.dashboard'))
@section('page-subtitle', __('app.admin.dashboard_subtitle'))

@section('content')

{{-- KPI Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 mb-8">
    @php
        $cards = [
            ['label' => __('app.admin.total_users'),        'value' => $stats['total_users'],        'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'text-blue-600 dark:text-blue-400', 'bg' => 'bg-blue-50 dark:bg-blue-900/20', 'fmt' => false],
            ['label' => __('app.admin.admin_users'),        'value' => $stats['admin_users'],        'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'text-purple-600 dark:text-purple-400', 'bg' => 'bg-purple-50 dark:bg-purple-900/20', 'fmt' => false],
            ['label' => __('app.admin.total_transactions'), 'value' => $stats['total_transactions'], 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'text-slate-600 dark:text-slate-300', 'bg' => 'bg-slate-100 dark:bg-slate-800', 'fmt' => false],
            ['label' => __('app.admin.total_expenses'),     'value' => $stats['total_expenses'],     'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'text-red-600 dark:text-red-400', 'bg' => 'bg-red-50 dark:bg-red-900/20', 'fmt' => true],
            ['label' => __('app.admin.total_income'),       'value' => $stats['total_income'],       'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'text-green-600 dark:text-green-400', 'bg' => 'bg-green-50 dark:bg-green-900/20', 'fmt' => true],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 flex items-start gap-4">
            <div class="w-10 h-10 rounded-xl {{ $card['bg'] }} flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 {{ $card['color'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                </svg>
            </div>
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-0.5">{{ $card['label'] }}</p>
                <p class="text-xl font-bold {{ $card['color'] }}">
                    @if($card['fmt'])
                        R$ {{ number_format($card['value'], 2, ',', '.') }}
                    @else
                        {{ $card['value'] }}
                    @endif
                </p>
            </div>
        </div>
    @endforeach
</div>

{{-- Recent Users --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
        <h3 class="font-semibold text-slate-800 dark:text-white">{{ __('app.admin.recent_users') }}</h3>
        <a href="{{ route('admin.users.index') }}" class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
            {{ __('app.dashboard.view_all') }}
        </a>
    </div>
    <div class="divide-y divide-slate-100 dark:divide-slate-800">
        @foreach($recentUsers as $user)
            <div class="flex items-center gap-4 px-5 py-3.5">
                <div class="w-9 h-9 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-bold flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-slate-800 dark:text-white truncate">{{ $user->name }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    @if($user->isAdmin())
                        <span class="text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-300 px-2 py-0.5 rounded-full font-medium">
                            {{ __('app.admin.admin_badge') }}
                        </span>
                    @endif
                    <span class="text-xs text-slate-400">{{ $user->created_at->isoFormat('DD MMM YYYY') }}</span>
                    <a href="{{ route('admin.users.edit', $user) }}"
                       class="text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                    </a>
                </div>
            </div>
        @endforeach
    </div>
</div>

@endsection
