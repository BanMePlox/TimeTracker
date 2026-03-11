@extends('layouts.admin')

@section('title', 'Editar Ausencia')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.ausencias.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a ausencias
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Editar ausencia</h3>

        <form action="{{ route('admin.ausencias.update', $ausencia) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Empleado -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1.5">Empleado</label>
                <select id="user_id" name="user_id"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 @error('user_id') border-red-300 @enderror">
                    <option value="">Seleccionar empleado...</option>
                    @foreach($usuarios as $usuario)
                        <option value="{{ $usuario->id }}" {{ old('user_id', $ausencia->user_id) == $usuario->id ? 'selected' : '' }}>
                            {{ $usuario->name }}
                        </option>
                    @endforeach
                </select>
                @error('user_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tipo -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de ausencia</label>
                <select id="tipo" name="tipo"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 @error('tipo') border-red-300 @enderror">
                    <option value="">Seleccionar tipo...</option>
                    <option value="vacaciones" {{ old('tipo', $ausencia->tipo) === 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                    <option value="baja_medica" {{ old('tipo', $ausencia->tipo) === 'baja_medica' ? 'selected' : '' }}>Baja médica</option>
                    <option value="ausencia_justificada" {{ old('tipo', $ausencia->tipo) === 'ausencia_justificada' ? 'selected' : '' }}>Ausencia justificada</option>
                    <option value="ausencia_injustificada" {{ old('tipo', $ausencia->tipo) === 'ausencia_injustificada' ? 'selected' : '' }}>Ausencia injustificada</option>
                </select>
                @error('tipo')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Fecha inicio y fin -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-1.5">Fecha inicio</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio"
                           value="{{ old('fecha_inicio', $ausencia->fecha_inicio->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 @error('fecha_inicio') border-red-300 @enderror">
                    @error('fecha_inicio')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-1.5">Fecha fin</label>
                    <input type="date" id="fecha_fin" name="fecha_fin"
                           value="{{ old('fecha_fin', $ausencia->fecha_fin->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 @error('fecha_fin') border-red-300 @enderror">
                    @error('fecha_fin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dynamic days count -->
            <div id="dias-container" class="hidden">
                <div class="px-4 py-3 bg-blue-50 border border-blue-200 rounded-xl">
                    <p class="text-sm text-blue-800 font-medium">
                        Duración: <span id="dias-count" class="font-bold"></span> día(s)
                    </p>
                </div>
            </div>

            <!-- Descripcion -->
            <div>
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Descripción
                    <span class="text-gray-400 font-normal">(opcional)</span>
                </label>
                <textarea id="descripcion" name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900 resize-none @error('descripcion') border-red-300 @enderror"
                          placeholder="Observaciones o motivo...">{{ old('descripcion', $ausencia->descripcion) }}</textarea>
                @error('descripcion')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Estado -->
            <div>
                <label for="estado" class="block text-sm font-medium text-gray-700 mb-1.5">Estado</label>
                <select id="estado" name="estado"
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900 @error('estado') border-red-300 @enderror">
                    <option value="pendiente" {{ old('estado', $ausencia->estado) === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="aprobada" {{ old('estado', $ausencia->estado) === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                    <option value="rechazada" {{ old('estado', $ausencia->estado) === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
                </select>
                @error('estado')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Actions -->
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
                    Guardar cambios
                </button>
                <a href="{{ route('admin.ausencias.index') }}"
                   class="px-6 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function calcDays() {
        const inicio = document.getElementById('fecha_inicio').value;
        const fin = document.getElementById('fecha_fin').value;
        const container = document.getElementById('dias-container');
        const count = document.getElementById('dias-count');

        if (inicio && fin) {
            const d1 = new Date(inicio);
            const d2 = new Date(fin);
            if (d2 >= d1) {
                const diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1;
                count.textContent = diff;
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        } else {
            container.classList.add('hidden');
        }
    }

    document.getElementById('fecha_inicio').addEventListener('change', calcDays);
    document.getElementById('fecha_fin').addEventListener('change', calcDays);
    calcDays();
</script>
@endpush
@endsection
