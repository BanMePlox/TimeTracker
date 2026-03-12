@extends('layouts.empleado')

@section('title', 'Solicitar ausencia')

@section('content')
<div class="max-w-xl">
    <div class="mb-6">
        <a href="{{ route('empleado.ausencias.index') }}" class="inline-flex items-center text-gray-400 hover:text-gray-600 text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-indigo-50">
            <h2 class="text-lg font-semibold text-gray-900">Nueva solicitud de ausencia</h2>
            <p class="text-gray-500 text-sm mt-0.5">Se enviará al administrador para su aprobación</p>
        </div>

        <form action="{{ route('empleado.ausencias.store') }}" method="POST" class="p-6 space-y-5">
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
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Tipo de ausencia *</label>
                <select name="tipo" required
                        class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                    <option value="">Selecciona un tipo...</option>
                    <option value="vacaciones" {{ old('tipo') === 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                    <option value="baja_medica" {{ old('tipo') === 'baja_medica' ? 'selected' : '' }}>Baja médica</option>
                    <option value="ausencia_justificada" {{ old('tipo') === 'ausencia_justificada' ? 'selected' : '' }}>Ausencia justificada</option>
                    <option value="ausencia_injustificada" {{ old('tipo') === 'ausencia_injustificada' ? 'selected' : '' }}>Ausencia injustificada</option>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha inicio *</label>
                    <input type="date" name="fecha_inicio" value="{{ old('fecha_inicio') }}"
                           min="{{ today()->format('Y-m-d') }}" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Fecha fin *</label>
                    <input type="date" name="fecha_fin" value="{{ old('fecha_fin') }}"
                           min="{{ today()->format('Y-m-d') }}" required
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descripción (opcional)</label>
                <textarea name="descripcion" rows="3"
                          class="w-full border border-gray-300 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                          placeholder="Motivo o detalles adicionales...">{{ old('descripcion') }}</textarea>
            </div>

            <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl text-sm flex items-start gap-2">
                <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                Tu solicitud quedará como <strong>pendiente</strong> hasta que el administrador la apruebe o rechace.
            </div>

            <div class="flex items-center justify-end gap-3 pt-2 border-t border-gray-100">
                <a href="{{ route('empleado.ausencias.index') }}"
                   class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium transition-colors text-sm">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors text-sm shadow-sm">
                    Enviar solicitud
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
