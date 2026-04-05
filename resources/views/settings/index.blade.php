@extends('layouts.app')

@section('title', 'Configurações')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800 dark:text-white">Configurações</h1>
        <p class="text-slate-500 dark:text-slate-400 text-sm mt-1">Gerencie suas integrações e preferências.</p>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4 text-sm text-green-700 dark:text-green-400">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4 text-sm text-red-700 dark:text-red-400">
            {{ session('error') }}
        </div>
    @endif

    {{-- OpenAI Card --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">

        <div class="flex items-center gap-3 mb-5">
            <div class="w-10 h-10 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                <svg class="w-5 h-5 text-slate-600 dark:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-semibold text-slate-800 dark:text-white">Análise com IA</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400">Configure sua chave da OpenAI para usar a análise financeira inteligente.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        API Key da OpenAI
                    </label>

                    @if(auth()->user()->openai_key)
                        <p class="text-xs text-green-600 dark:text-green-400 mb-2 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Chave configurada. Preencha abaixo para substituir.
                        </p>
                    @endif

                    <input type="password"
                           name="openai_key"
                           placeholder="{{ auth()->user()->openai_key ? '••••••••••••••••••••••••' : 'sk-...' }}"
                           autocomplete="off"
                           class="w-full px-4 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-mono">

                    @error('openai_key')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-xs text-slate-500 dark:text-slate-400">
                        Sua chave é armazenada de forma encriptada. Obtenha a sua em
                        <span class="text-blue-600 dark:text-blue-400">platform.openai.com/api-keys</span>.
                    </p>
                </div>

                @if(auth()->user()->openai_key)
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="remove_key" id="remove_key" value="1"
                               class="w-4 h-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                        <label for="remove_key" class="text-sm text-slate-600 dark:text-slate-400 cursor-pointer">
                            Remover chave existente
                        </label>
                    </div>
                @endif

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-5 rounded-lg text-sm transition-colors">
                        Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>

</div>
@endsection
