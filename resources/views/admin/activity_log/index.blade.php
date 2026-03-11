@extends('layouts.admin')

@section('title', 'Log de actividad')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="dm-title text-2xl font-bold text-gray-800">Log de actividad</h3>
        <p class="dm-muted text-gray-500 text-sm mt-1">Registro inmutable de todas las acciones de administración</p>
    </div>
    <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400 px-4 py-2 rounded-lg text-sm">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
        </svg>
        Los registros no pueden modificarse ni eliminarse
    </div>
</div>

{{-- Filtros --}}
<div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div>
            <label class="dm-label block text-xs font-medium text-gray-500 mb-1">Administrador</label>
            <select name="user_id" class="dm-select w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todos</option>
                @foreach($admins as $admin)
                    <option value="{{ $admin->id }}" {{ request('user_id') == $admin->id ? 'selected' : '' }}>
                        {{ $admin->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="dm-label block text-xs font-medium text-gray-500 mb-1">Acción</label>
            <select name="accion" class="dm-select w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Todas</option>
                @foreach($acciones as $accion)
                    <option value="{{ $accion }}" {{ request('accion') === $accion ? 'selected' : '' }}>
                        {{ str_replace('_', ' ', ucfirst($accion)) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="dm-label block text-xs font-medium text-gray-500 mb-1">Desde</label>
            <input type="date" name="fecha_desde" value="{{ request('fecha_desde') }}"
                   class="dm-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="dm-label block text-xs font-medium text-gray-500 mb-1">Hasta</label>
            <input type="date" name="fecha_hasta" value="{{ request('fecha_hasta') }}"
                   class="dm-input w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="sm:col-span-2 lg:col-span-4 flex gap-3">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Filtrar
            </button>
            <a href="{{ route('admin.activity-log.index') }}" class="dm-link-card bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">
                Limpiar
            </a>
        </div>
    </form>
</div>

{{-- Tabla --}}
<div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="dm-border p-5 border-b border-gray-100 flex items-center justify-between">
        <span class="dm-title font-semibold text-gray-900">
            {{ $logs->total() }} {{ $logs->total() === 1 ? 'registro' : 'registros' }}
        </span>
        <span class="dm-muted text-xs text-gray-400">Mostrando {{ $logs->firstItem() }}–{{ $logs->lastItem() }}</span>
    </div>

    @if($logs->isEmpty())
        <div class="p-12 text-center dm-muted text-gray-400">
            <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            No hay registros que coincidan con los filtros.
        </div>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="dm-thead bg-gray-50">
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Fecha y hora</th>
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Administrador</th>
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Acción</th>
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">Descripción</th>
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-5 py-3">IP</th>
                </tr>
            </thead>
            <tbody class="dm-divide divide-y divide-gray-50">
                @foreach($logs as $log)
                <tr class="dm-row hover:bg-gray-50 transition-colors">
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="dm-text text-gray-700 font-mono text-xs">{{ $log->created_at->format('d/m/Y H:i:s') }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @if($log->user)
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($log->user->name, 0, 1)) }}
                                </div>
                                <span class="dm-title text-gray-800 font-medium">{{ $log->user->name }}</span>
                            </div>
                        @else
                            <span class="dm-muted text-gray-400 italic">Sistema</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        @php
                            $colores = [
                                'login'               => 'bg-blue-100 text-blue-700',
                                'logout'              => 'bg-gray-100 text-gray-600',
                                'usuario_creado'      => 'bg-green-100 text-green-700',
                                'usuario_actualizado' => 'bg-yellow-100 text-yellow-700',
                                'usuario_eliminado'   => 'bg-red-100 text-red-700',
                                'pin_regenerado'      => 'bg-purple-100 text-purple-700',
                                'fichaje_corregido'   => 'bg-orange-100 text-orange-700',
                                'fichaje_eliminado'   => 'bg-red-100 text-red-700',
                                'ausencia_creada'     => 'bg-green-100 text-green-700',
                                'ausencia_actualizada'=> 'bg-yellow-100 text-yellow-700',
                                'ausencia_eliminada'  => 'bg-red-100 text-red-700',
                                'ausencia_aprobada'   => 'bg-emerald-100 text-emerald-700',
                                'ausencia_rechazada'  => 'bg-red-100 text-red-700',
                            ];
                            $color = $colores[$log->accion] ?? 'bg-gray-100 text-gray-600';
                        @endphp
                        <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $color }}">
                            {{ str_replace('_', ' ', ucfirst($log->accion)) }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="dm-text text-gray-700">{{ $log->descripcion }}</span>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap">
                        <span class="dm-muted text-gray-400 font-mono text-xs">{{ $log->ip ?? '—' }}</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
    <div class="dm-border px-5 py-4 border-t border-gray-100">
        {{ $logs->withQueryString()->links() }}
    </div>
    @endif
    @endif
</div>
@endsection
