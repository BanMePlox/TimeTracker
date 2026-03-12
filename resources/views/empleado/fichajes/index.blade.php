@extends('layouts.empleado')

@section('title', 'Mis fichajes')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mis fichajes</h2>
        <p class="text-gray-400 text-sm mt-0.5">Historial de entradas y salidas</p>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-4 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde', $desde->format('Y-m-d')) }}"
                   class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta', $hasta->format('Y-m-d')) }}"
                   class="border border-gray-300 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
            Filtrar
        </button>
        <a href="{{ route('empleado.fichajes.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-xl text-sm transition-colors">
            Limpiar
        </a>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <span class="font-semibold text-gray-900">{{ $fichajes->total() }} registros</span>
    </div>

    @if($fichajes->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            No hay fichajes en el período seleccionado.
        </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Tipo</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Fecha</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Hora</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($fichajes as $fichaje)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-3">
                    @if($fichaje->tipo === 'entrada')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span>
                            Entrada
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-1.5"></span>
                            Salida
                        </span>
                    @endif
                </td>
                <td class="px-6 py-3 text-gray-700">{{ $fichaje->created_at->format('d/m/Y') }}</td>
                <td class="px-6 py-3 text-gray-700 font-mono">{{ $fichaje->created_at->format('H:i:s') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($fichajes->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $fichajes->withQueryString()->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
