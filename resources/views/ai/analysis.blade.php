@extends('layouts.app')

@section('title', __('app.ai.page_title'))
@section('page-title', __('app.ai.page_title'))
@section('page-subtitle', __('app.ai.page_subtitle'))

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- Header card --}}
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 dark:from-emerald-700 dark:to-emerald-900 rounded-2xl p-6 mb-6 text-white">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center flex-shrink-0">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-lg font-bold">{{ __('app.ai.card_title') }}</h2>
                <p class="text-emerald-100 text-sm mt-1">{{ __('app.ai.card_desc') }}</p>
            </div>
        </div>
    </div>

    @if(!$isConfigured)
        {{-- API not configured --}}
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-5">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="font-semibold text-amber-800 dark:text-amber-300">{{ __('app.ai.api_not_configured') }}</p>
                    <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">
                        {{ __('app.ai.api_config_hint') }}
                        <a href="{{ route('settings.index') }}" class="underline font-medium hover:text-amber-900 dark:hover:text-amber-200">{{ __('app.ai.settings_link') }}</a>.
                    </p>
                </div>
            </div>
        </div>
    @else
        {{-- Trigger form --}}
        <form method="POST" action="{{ route('ai.analyze') }}" x-data="{ loading: false }">
            @csrf
            <button type="submit"
                    @click="loading = true"
                    :disabled="loading"
                    class="w-full flex items-center justify-center gap-3 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 disabled:cursor-not-allowed text-white font-semibold py-3.5 px-6 rounded-xl text-sm transition-colors">
                <template x-if="!loading">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </template>
                <template x-if="loading">
                    <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </template>
                <span x-text="loading ? '{{ __('app.ai.analyzing') }}' : '{{ __('app.ai.generate_btn') }}'"></span>
            </button>
        </form>
    @endif

    {{-- Analysis result --}}
    @if($analysis)
        <div class="mt-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
                <div class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></div>
                <p class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ __('app.ai.analysis_generated') }} {{ now()->format('d/m/Y \à\s H:i') }}</p>
            </div>

            {{-- Rendered markdown --}}
            <div id="aiContent" class="p-6 prose prose-slate dark:prose-invert max-w-none text-sm leading-relaxed"></div>
        </div>
    @endif

</div>

@endsection

@push('scripts')
<script>
@if($analysis)
    const raw = @json($analysis);
    document.getElementById('aiContent').innerHTML = marked.parse(raw);

    // Style the rendered markdown to match our design
    document.querySelectorAll('#aiContent h2').forEach(el => {
        el.className = 'text-base font-bold text-slate-800 dark:text-white mt-6 mb-2 pb-2 border-b border-slate-100 dark:border-slate-800';
    });
    document.querySelectorAll('#aiContent h3').forEach(el => {
        el.className = 'text-sm font-semibold text-slate-700 dark:text-slate-200 mt-4 mb-1.5';
    });
    document.querySelectorAll('#aiContent ul').forEach(el => {
        el.className = 'space-y-1.5 my-2';
    });
    document.querySelectorAll('#aiContent li').forEach(el => {
        el.className = 'text-slate-600 dark:text-slate-300 text-sm flex gap-2';
    });
    document.querySelectorAll('#aiContent p').forEach(el => {
        el.className = 'text-slate-600 dark:text-slate-300 text-sm my-2';
    });
    document.querySelectorAll('#aiContent strong').forEach(el => {
        el.className = 'text-slate-800 dark:text-white font-semibold';
    });
@endif
</script>
@endpush
