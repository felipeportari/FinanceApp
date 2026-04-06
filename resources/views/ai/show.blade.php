@extends('layouts.app')

@section('title', __('app.ai.page_title'))
@section('page-title', __('app.ai.page_title'))
@section('page-subtitle')
{{ __('app.ai.report_generated') }} <span id="reportSubDate" data-utc="{{ $report->created_at->toISOString() }}">{{ $report->created_at->isoFormat('DD/MM/YYYY') }}</span>
@endsection

@section('content')

<div class="max-w-4xl mx-auto space-y-5">

    {{-- Actions bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('ai.analysis') }}"
           class="flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('app.ai.back_to_list') }}
        </a>

        <form method="POST" action="{{ route('ai.destroy', $report) }}"
              onsubmit="return confirm('{{ __('app.ai.delete_confirm') }}')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="flex items-center gap-1.5 text-sm text-slate-400 hover:text-red-500 dark:hover:text-red-400 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                {{ __('app.ai.delete_report') }}
            </button>
        </form>
    </div>

    {{-- Report content --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/50">
            <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
            <p class="text-sm font-medium text-slate-700 dark:text-slate-200">
                {{ __('app.ai.report_generated') }}
                <span id="reportHeaderDate" data-utc="{{ $report->created_at->toISOString() }}">
                    {{ $report->created_at->isoFormat('DD [de] MMMM [de] YYYY [às] HH:mm') }}
                </span>
            </p>
        </div>

        <div id="reportContent" class="p-6 prose prose-slate dark:prose-invert max-w-none text-sm leading-relaxed"></div>
    </div>

</div>

@endsection

@push('scripts')
<script>
// Format timestamps in user's local timezone
[document.getElementById('reportSubDate'), document.getElementById('reportHeaderDate')].forEach((el, i) => {
    if (!el) return;
    const d = new Date(el.dataset.utc);
    el.textContent = i === 0
        ? d.toLocaleString(undefined, { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' })
        : d.toLocaleString(undefined, { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
});

const raw = @json($report->content);
document.getElementById('reportContent').innerHTML = marked.parse(raw);

document.querySelectorAll('#reportContent h2').forEach(el => {
    el.className = 'text-base font-bold text-slate-800 dark:text-white mt-6 mb-3 pb-2 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2';
});
document.querySelectorAll('#reportContent h3').forEach(el => {
    el.className = 'text-sm font-semibold text-slate-700 dark:text-slate-200 mt-4 mb-1.5';
});
document.querySelectorAll('#reportContent ul').forEach(el => {
    el.className = 'space-y-1.5 my-2 pl-4';
});
document.querySelectorAll('#reportContent li').forEach(el => {
    el.className = 'text-slate-600 dark:text-slate-300 text-sm';
});
document.querySelectorAll('#reportContent p').forEach(el => {
    el.className = 'text-slate-600 dark:text-slate-300 text-sm my-2';
});
document.querySelectorAll('#reportContent strong').forEach(el => {
    el.className = 'text-slate-800 dark:text-white font-semibold';
});
document.querySelectorAll('#reportContent hr').forEach(el => {
    el.className = 'border-slate-200 dark:border-slate-700 my-4';
});
</script>
@endpush
