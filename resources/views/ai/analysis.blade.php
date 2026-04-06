@extends('layouts.app')

@section('title', __('app.ai.page_title'))
@section('page-title', __('app.ai.page_title'))
@section('page-subtitle', __('app.ai.page_subtitle'))

@section('content')

<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header card --}}
    <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 dark:from-emerald-700 dark:to-emerald-900 rounded-2xl p-6 text-white">
        <div class="flex items-start justify-between gap-4">
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

            @if($isConfigured)
                {{-- Generate button --}}
                <div x-data="aiGenerator()" class="flex-shrink-0">
                    <button @click="generate()"
                            :disabled="loading"
                            class="flex items-center gap-2 bg-white/20 hover:bg-white/30 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-2.5 px-5 rounded-xl text-sm transition-colors whitespace-nowrap">
                        <template x-if="!loading">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </template>
                        <template x-if="loading">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="loading ? '{{ __('app.ai.analyzing') }}' : '{{ __('app.ai.generate_btn') }}'"></span>
                    </button>

                    {{-- Error alert --}}
                    <div x-show="error" x-transition
                         class="mt-3 flex items-start gap-2 bg-red-500/20 border border-red-300/30 rounded-lg px-3 py-2 text-sm text-white max-w-xs">
                        <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span x-text="error"></span>
                    </div>
                </div>
            @endif
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
    @endif

    {{-- Reports list --}}
    <div>
        <h3 class="text-base font-semibold text-slate-800 dark:text-white mb-4">{{ __('app.ai.past_reports') }}</h3>

        @if($reports->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 py-16 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-7 h-7 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <p class="text-slate-500 dark:text-slate-400 font-medium">{{ __('app.ai.no_reports') }}</p>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-1">{{ __('app.ai.no_reports_hint') }}</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($reports as $report)
                    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-5 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 dark:text-white">
                                {{ __('app.ai.report_generated') }}
                                <span class="local-date" data-utc="{{ $report->created_at->toISOString() }}">
                                    {{ $report->created_at->isoFormat('DD/MM/YYYY [às] HH:mm') }}
                                </span>
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ $report->created_at->diffForHumans() }}
                            </p>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('ai.show', $report) }}"
                               class="text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
                                {{ __('app.ai.view_report') }}
                            </a>
                            <form method="POST" action="{{ route('ai.destroy', $report) }}"
                                  onsubmit="return confirm('{{ __('app.ai.delete_confirm') }}')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors ml-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

@endsection

@push('scripts')
<script>
document.querySelectorAll('.local-date[data-utc]').forEach(el => {
    const d = new Date(el.dataset.utc);
    el.textContent = d.toLocaleString(undefined, {
        day: '2-digit', month: '2-digit', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
});

function aiGenerator() {
    return {
        loading: false,
        error: null,

        async generate() {
            this.loading = true;
            this.error   = null;

            try {
                const res = await fetch('{{ route('ai.store') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept':       'application/json',
                        'Content-Type': 'application/json',
                    },
                    signal: AbortSignal.timeout(110_000),
                });

                const data = await res.json();

                if (!res.ok) {
                    this.error = data.error ?? '{{ __('app.messages.openai_error') }}';
                    this.loading = false;
                    return;
                }

                window.location.href = data.redirect;

            } catch (err) {
                this.error   = '{{ __('app.messages.openai_error') }}';
                this.loading = false;
            }
        }
    };
}
</script>
@endpush
