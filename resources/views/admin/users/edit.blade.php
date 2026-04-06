@extends('layouts.app')

@section('title', __('app.admin.edit_user'))
@section('page-title', __('app.admin.edit_user'))
@section('page-subtitle', __('app.admin.edit_subtitle'))

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.admin.name_label') }}</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.admin.email_label') }}</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                @error('email')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.admin.password_edit_label') }}</label>
                <input type="password" name="password"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('app.admin.password_edit_hint') }}</p>
                @error('password')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Confirm --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.admin.password_confirm') }}</label>
                <input type="password" name="password_confirmation"
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
            </div>

            {{-- Locale --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">{{ __('app.admin.locale_label') }}</label>
                <select name="locale" required
                        class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    <option value="pt" {{ old('locale', $user->locale) === 'pt' ? 'selected' : '' }}>{{ __('app.settings.lang_pt') }}</option>
                    <option value="en" {{ old('locale', $user->locale) === 'en' ? 'selected' : '' }}>{{ __('app.settings.lang_en') }}</option>
                    <option value="es" {{ old('locale', $user->locale) === 'es' ? 'selected' : '' }}>{{ __('app.settings.lang_es') }}</option>
                </select>
                @error('locale')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Is Admin --}}
            <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg">
                <input type="checkbox" name="is_admin" id="is_admin" value="1"
                       {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}
                       {{ $user->id === auth()->id() ? 'disabled' : '' }}
                       class="mt-0.5 w-4 h-4 rounded border-slate-300 text-purple-600 focus:ring-purple-500">
                <div>
                    <label for="is_admin" class="text-sm font-medium text-slate-700 dark:text-slate-300 cursor-pointer">
                        {{ __('app.admin.admin_label') }}
                    </label>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ __('app.admin.admin_hint') }}</p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors">
                    {{ __('app.admin.update_user') }}
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="px-6 py-2.5 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium rounded-lg text-sm transition-colors">
                    {{ __('app.common.cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
