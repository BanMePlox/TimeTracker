@extends('layouts.admin')

@section('title', 'Corregir Fichaje')

@section('content')
<div class="max-w-lg">
    <div class="mb-6">
        <a href="{{ route('admin.fichajes.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a fichajes
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Corregir fichaje</h3>

        <form action="{{ route('admin.fichajes.update', $fichaje) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Employee (readonly) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Empleado</label>
                <div class="flex items-center px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm mr-3">
                        {{ substr($fichaje->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-gray-900 font-medium text-sm">{{ $fichaje->user->name }}</p>
                        <p class="text-gray-400 text-xs">{{ $fichaje->user->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1.5">Tipo</label>
                <select id="tipo" name="tipo"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 @error('tipo') border-red-300 @enderror">
                    <option value="entrada" {{ old('tipo', $fichaje->tipo) === 'entrada' ? 'selected' : '' }}>Entrada</option>
                    <option value="salida" {{ old('tipo', $fichaje->tipo) === 'salida' ? 'selected' : '' }}>Salida</option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha -->
            <div>
                <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1.5">Fecha</label>
                <input type="date" id="fecha" name="fecha"
                       value="{{ old('fecha', $fichaje->created_at->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 @error('fecha') border-red-300 @enderror">
                @error('fecha')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Hora -->
            <div>
                <label for="hora" class="block text-sm font-medium text-gray-700 mb-1.5">Hora</label>
                <input type="time" id="hora" name="hora"
                       value="{{ old('hora', $fichaje->created_at->format('H:i')) }}"
                       step="1"
                       class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 @error('hora') border-red-300 @enderror">
                @error('hora')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.fichajes.index') }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
