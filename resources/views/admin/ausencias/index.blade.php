@extends('layouts.admin')

@section('title', 'Ausencias y Vacaciones')

@section('content')
<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Filtros</h3>
    <form action="{{ route('admin.ausencias.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Empleado</label>
            <select name="user_id" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                <option value="">Todos los empleados</option>
                @foreach($usuarios as $usuario)
                    <option value="{{ $usuario->id }}" {{ request('user_id') == $usuario->id ? 'selected' : '' }}>
                        {{ $usuario->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Tipo</label>
            <select name="tipo" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                <option value="">Todos</option>
                <option value="vacaciones" {{ request('tipo') === 'vacaciones' ? 'selected' : '' }}>Vacaciones</option>
                <option value="baja_medica" {{ request('tipo') === 'baja_medica' ? 'selected' : '' }}>Baja médica</option>
                <option value="ausencia_justificada" {{ request('tipo') === 'ausencia_justificada' ? 'selected' : '' }}>Ausencia justificada</option>
                <option value="ausencia_injustificada" {{ request('tipo') === 'ausencia_injustificada' ? 'selected' : '' }}>Ausencia injustificada</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Estado</label>
            <select name="estado" class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                <option value="">Todos</option>
                <option value="pendiente" {{ request('estado') === 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                <option value="aprobada" {{ request('estado') === 'aprobada' ? 'selected' : '' }}>Aprobada</option>
                <option value="rechazada" {{ request('estado') === 'rechazada' ? 'selected' : '' }}>Rechazada</option>
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
                Filtrar
            </button>
            <a href="{{ route('admin.ausencias.index') }}"
               class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
                Limpiar
            </a>
        </div>
    </form>
</div>

<!-- Results -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Ausencias y vacaciones</h3>
            <p class="text-gray-500 text-sm">{{ $ausencias->total() }} registros encontrados</p>
        </div>
        <a href="{{ route('admin.ausencias.create') }}"
           class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nueva ausencia
        </a>
    </div>

    @if($ausencias->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p>No se encontraron ausencias con los filtros aplicados.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Empleado</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Tipo</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Desde</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Hasta</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Días</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Estado</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($ausencias as $ausencia)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm mr-3">
                                {{ substr($ausencia->user->name, 0, 1) }}
                            </div>
                            <p class="text-gray-900 font-medium text-sm">{{ $ausencia->user->name }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 text-sm">{{ $ausencia->tipo_label }}</td>
                    <td class="px-6 py-4 text-gray-700 text-sm">{{ $ausencia->fecha_inicio->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-gray-700 text-sm">{{ $ausencia->fecha_fin->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-gray-700 text-sm font-semibold">{{ $ausencia->dias }}</td>
                    <td class="px-6 py-4">
                        @php $color = $ausencia->estado_color; @endphp
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                            {{ $color === 'yellow' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $color === 'green' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $color === 'red' ? 'bg-red-100 text-red-800' : '' }}
                            {{ $color === 'gray' ? 'bg-gray-100 text-gray-800' : '' }}">
                            {{ ucfirst($ausencia->estado) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if($ausencia->estado === 'pendiente')
                                <form action="{{ route('admin.ausencias.aprobar', $ausencia) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-2 py-1 text-xs font-medium text-green-700 bg-green-50 hover:bg-green-100 rounded-lg transition-colors"
                                            title="Aprobar">
                                        Aprobar
                                    </button>
                                </form>
                                <form action="{{ route('admin.ausencias.rechazar', $ausencia) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit"
                                            class="px-2 py-1 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 rounded-lg transition-colors"
                                            title="Rechazar">
                                        Rechazar
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('admin.ausencias.edit', $ausencia) }}"
                               class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Editar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form action="{{ route('admin.ausencias.destroy', $ausencia) }}" method="POST"
                                  onsubmit="return confirm('¿Eliminar esta ausencia?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Eliminar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($ausencias->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $ausencias->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
