@extends('layouts.app')

@section('title', __('app.admin.users'))
@section('page-title', __('app.admin.users'))
@section('page-subtitle', __('app.admin.users_subtitle'))

@section('content')

{{-- Search + New --}}
<div class="flex flex-wrap items-center gap-3 mb-5">
    <form method="GET" action="{{ route('admin.users.index') }}" class="flex-1 min-w-64">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="{{ __('app.admin.search_users') }}"
               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </form>
    @if(request('search'))
        <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:underline">
            {{ __('app.transactions.clear') }}
        </a>
    @endif
    <a href="{{ route('admin.users.create') }}"
       class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        {{ __('app.admin.new_user') }}
    </a>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
    @if($users->isEmpty())
        <div class="py-16 text-center text-slate-400">
            {{ __('app.admin.no_users') }}
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50">
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.admin.col_user') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.admin.col_role') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.admin.col_locale') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.admin.col_transactions') }}</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">{{ __('app.admin.col_created') }}</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($users as $user)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/30">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-medium text-slate-800 dark:text-white truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($user->isAdmin())
                                    <span class="inline-flex text-xs font-medium px-2.5 py-1 rounded-full bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300">
                                        {{ __('app.admin.role_admin') }}
                                    </span>
                                @else
                                    <span class="inline-flex text-xs font-medium px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400">
                                        {{ __('app.admin.role_user') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400 uppercase text-xs font-medium">
                                {{ $user->locale }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400">
                                {{ $user->transactions_count }}
                            </td>
                            <td class="px-5 py-3.5 text-slate-500 dark:text-slate-400 text-xs">
                                {{ $user->created_at->isoFormat('DD/MM/YYYY') }}
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm('{{ __('app.admin.delete_confirm', ['name' => $user->name]) }}')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-slate-400 hover:text-red-600 dark:hover:text-red-400 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    @endif
</div>

@endsection
