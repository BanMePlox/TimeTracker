@extends('layouts.admin')

@section('title', 'Informe de Horas')

@section('content')
<!-- Filters -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6">
    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wider mb-4">Filtros</h3>
    <form action="{{ route('admin.informes.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
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
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Desde</label>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde', \Carbon\Carbon::now()->subDays(29)->toDateString()) }}"
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                   class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-900">
        </div>

        <div class="flex items-end gap-2">
            <button type="submit"
                    class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
                Filtrar
            </button>
            <a href="{{ route('admin.informes.index') }}"
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
            <h3 class="text-lg font-semibold text-gray-900">Informe de horas trabajadas</h3>
            <p class="text-gray-500 text-sm">{{ count($informe) }} sesiones encontradas</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.informes.resumen') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl text-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Resumen mensual
            </a>
            <a href="{{ route('admin.informes.pdf') }}?{{ http_build_query(request()->only(['user_id'])) }}"
               target="_blank"
               class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl text-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                PDF mensual
            </a>
            <a href="{{ route('admin.informes.export') }}?{{ http_build_query(request()->only(['user_id','fecha_desde','fecha_hasta'])) }}"
               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-xl text-sm transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Exportar CSV
            </a>
        </div>
    </div>

    @if(empty($informe))
        <div class="p-12 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p>No se encontraron sesiones de trabajo con los filtros aplicados.</p>
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Empleado</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Fecha</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Entrada</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Salida</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Horas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($informe as $fila)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm mr-3">
                                {{ substr($fila['usuario']->name, 0, 1) }}
                            </div>
                            <div>
                                <p class="text-gray-900 font-medium text-sm">{{ $fila['usuario']->name }}</p>
                                <p class="text-gray-400 text-xs">{{ $fila['usuario']->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-700 text-sm">
                        {{ \Carbon\Carbon::parse($fila['fecha'])->format('d/m/Y') }}
                    </td>
                    <td class="px-6 py-4 text-gray-700 font-mono text-sm">
                        {{ $fila['sesion']['entrada']->created_at->format('H:i:s') }}
                    </td>
                    <td class="px-6 py-4 font-mono text-sm">
                        @if($fila['sesion']['salida'])
                            <span class="text-gray-700">{{ $fila['sesion']['salida']->created_at->format('H:i:s') }}</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                En curso
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm">
                        @if($fila['sesion']['horas'] !== null)
                            <span class="font-semibold text-gray-900">{{ $fila['sesion']['horas'] }}h</span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
