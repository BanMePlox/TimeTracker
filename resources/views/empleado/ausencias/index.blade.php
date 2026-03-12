@extends('layouts.empleado')

@section('title', 'Mis ausencias')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900">Mis ausencias</h2>
        <p class="text-gray-400 text-sm mt-0.5">Vacaciones, bajas y otras ausencias</p>
    </div>
    <a href="{{ route('empleado.ausencias.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva solicitud
    </a>
</div>

<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($ausencias->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            No tienes ausencias registradas.
            <br>
            <a href="{{ route('empleado.ausencias.create') }}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">
                Crear primera solicitud →
            </a>
        </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50">
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Tipo</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Período</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Días</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Estado</th>
                <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Descripción</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @foreach($ausencias as $ausencia)
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-3 font-medium text-gray-800">{{ $ausencia->tipo_label }}</td>
                <td class="px-6 py-3 text-gray-600 font-mono text-xs">
                    {{ \Carbon\Carbon::parse($ausencia->fecha_inicio)->format('d/m/Y') }}
                    @if($ausencia->fecha_inicio !== $ausencia->fecha_fin)
                        → {{ \Carbon\Carbon::parse($ausencia->fecha_fin)->format('d/m/Y') }}
                    @endif
                </td>
                <td class="px-6 py-3 text-gray-600">{{ $ausencia->dias }}</td>
                <td class="px-6 py-3">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $ausencia->estado_color }}">
                        {{ ucfirst($ausencia->estado) }}
                    </span>
                </td>
                <td class="px-6 py-3 text-gray-400 text-xs max-w-xs truncate">
                    {{ $ausencia->descripcion ?? '—' }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($ausencias->hasPages())
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $ausencias->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
