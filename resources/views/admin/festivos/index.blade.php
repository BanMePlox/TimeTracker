@extends('layouts.admin')

@section('title', 'Festivos')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Festivos</h2>
        <p class="text-gray-400 text-sm mt-0.5">Días festivos excluidos del cómputo de horas</p>
    </div>
    <a href="{{ route('admin.festivos.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Añadir festivo
    </a>
</div>

<div class="bg-white dm-card rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    @if($festivos->isEmpty())
        <div class="p-12 text-center text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            No hay festivos configurados.
            <br>
            <a href="{{ route('admin.festivos.create') }}" class="text-blue-500 hover:underline text-sm mt-2 inline-block">
                Añadir el primero →
            </a>
        </div>
    @else
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 dm-thead">
                <th class="text-left text-xs font-semibold text-gray-500 dm-muted uppercase tracking-wider px-6 py-3">Fecha</th>
                <th class="text-left text-xs font-semibold text-gray-500 dm-muted uppercase tracking-wider px-6 py-3">Nombre</th>
                <th class="text-left text-xs font-semibold text-gray-500 dm-muted uppercase tracking-wider px-6 py-3">Descripción</th>
                <th class="text-left text-xs font-semibold text-gray-500 dm-muted uppercase tracking-wider px-6 py-3">Día</th>
                <th class="px-6 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50 dm-divide">
            @foreach($festivos as $festivo)
            <tr class="hover:bg-gray-50 dm-row transition-colors">
                <td class="px-6 py-3 font-mono text-sm dm-text text-gray-700">
                    {{ $festivo->fecha->format('d/m/Y') }}
                </td>
                <td class="px-6 py-3 font-medium dm-title text-gray-800">{{ $festivo->nombre }}</td>
                <td class="px-6 py-3 text-gray-400 dm-muted text-xs">{{ $festivo->descripcion ?? '—' }}</td>
                <td class="px-6 py-3 text-gray-500 dm-text text-xs">
                    {{ ucfirst($festivo->fecha->locale('es')->isoFormat('dddd')) }}
                </td>
                <td class="px-6 py-3 text-right">
                    <form action="{{ route('admin.festivos.destroy', $festivo) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este festivo?')">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="text-red-400 hover:text-red-600 text-xs font-medium transition-colors">
                            Eliminar
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @if($festivos->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dm-border">
        {{ $festivos->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
