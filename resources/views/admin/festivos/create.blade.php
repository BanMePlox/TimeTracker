@extends('layouts.admin')

@section('title', 'Añadir festivo')

@section('content')
<div class="max-w-lg">
    <div class="mb-6">
        <a href="{{ route('admin.festivos.index') }}" class="inline-flex items-center text-gray-400 hover:text-gray-600 text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a festivos
        </a>
    </div>

    <div class="bg-white dm-card rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 dm-border bg-gradient-to-r from-amber-50 to-orange-50">
            <h2 class="text-lg font-semibold text-gray-900">Nuevo festivo</h2>
            <p class="text-gray-500 text-sm mt-0.5">Este día se excluirá del cómputo de horas esperadas</p>
        </div>

        <form action="{{ route('admin.festivos.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium text-gray-700 dm-label mb-1.5">Fecha *</label>
                <input type="date" name="fecha" value="{{ old('fecha') }}" required
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dm-input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dm-label mb-1.5">Nombre *</label>
                <input type="text" name="nombre" value="{{ old('nombre') }}" required
                       placeholder="Ej: Día de la Constitución"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dm-input">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dm-label mb-1.5">Descripción (opcional)</label>
                <input type="text" name="descripcion" value="{{ old('descripcion') }}"
                       placeholder="Detalles adicionales..."
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dm-input">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100 dm-border">
                <a href="{{ route('admin.festivos.index') }}"
                   class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium transition-colors text-sm">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm">
                    Guardar festivo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
