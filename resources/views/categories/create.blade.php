@extends('layouts.app')

@section('title', 'Nova Categoria')
@section('page-title', 'Nova Categoria')
@section('page-subtitle', 'Crie uma categoria personalizada')

@section('content')

<div class="max-w-lg mx-auto">
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 p-6">

        <form method="POST" action="{{ route('categories.store') }}" class="space-y-5">
            @csrf

            {{-- Name --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Nome *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Ex: Viagens, Pets..."
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                @error('name')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Color --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Cor *</label>
                <div class="flex items-center gap-3">
                    <input type="color" name="color" value="{{ old('color', '#3B82F6') }}" id="colorPicker"
                           class="w-12 h-10 rounded-lg border border-slate-300 dark:border-slate-700 cursor-pointer bg-transparent">
                    <input type="text" id="colorHex" value="{{ old('color', '#3B82F6') }}"
                           placeholder="#3B82F6"
                           class="flex-1 px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm font-mono"
                           oninput="document.getElementById('colorPicker').value = this.value; document.querySelector('[name=color]').value = this.value">
                </div>

                {{-- Preset colors --}}
                <div class="flex flex-wrap gap-2 mt-3">
                    @foreach(['#EF4444','#F97316','#F59E0B','#22C55E','#0EA5E9','#3B82F6','#8B5CF6','#EC4899','#64748B','#16A34A'] as $hex)
                        <button type="button" onclick="setColor('{{ $hex }}')"
                                class="w-7 h-7 rounded-full border-2 border-white dark:border-slate-900 shadow-sm hover:scale-110 transition-transform"
                                style="background-color: {{ $hex }}" title="{{ $hex }}">
                        </button>
                    @endforeach
                </div>
                @error('color')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Icon --}}
            <div>
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Ícone (nome) *</label>
                <input type="text" name="icon" value="{{ old('icon', 'tag') }}"
                       placeholder="tag, home, star..."
                       class="w-full px-3 py-2.5 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Identificador de ícone para uso futuro.</p>
                @error('icon')
                    <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Actions --}}
            <div class="flex gap-3 pt-2">
                <button type="submit"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 px-4 rounded-lg text-sm transition-colors">
                    Criar Categoria
                </button>
                <a href="{{ route('categories.index') }}"
                   class="px-6 py-2.5 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 font-medium rounded-lg text-sm transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const picker = document.getElementById('colorPicker');
    const hex    = document.getElementById('colorHex');
    const hidden = document.querySelector('[name=color]');

    picker.addEventListener('input', () => {
        hex.value    = picker.value;
        hidden.value = picker.value;
    });

    function setColor(color) {
        picker.value = color;
        hex.value    = color;
        hidden.value = color;
    }
</script>
@endpush
