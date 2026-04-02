@if ($paginator->hasPages())
    <nav class="flex items-center justify-between">
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Mostrando <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paginator->firstItem() }}</span>
            a <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paginator->lastItem() }}</span>
            de <span class="font-medium text-slate-700 dark:text-slate-200">{{ $paginator->total() }}</span> resultados
        </p>

        <div class="flex gap-1">
            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <span class="px-3 py-1.5 text-sm rounded-lg text-slate-300 dark:text-slate-600 cursor-not-allowed">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-3 py-1.5 text-sm rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Anterior
                </a>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-3 py-1.5 text-sm text-slate-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="px-3 py-1.5 text-sm rounded-lg bg-blue-600 text-white font-medium">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}"
                               class="px-3 py-1.5 text-sm rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-3 py-1.5 text-sm rounded-lg text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800">
                    Próximo
                </a>
            @else
                <span class="px-3 py-1.5 text-sm rounded-lg text-slate-300 dark:text-slate-600 cursor-not-allowed">Próximo</span>
            @endif
        </div>
    </nav>
@endif
