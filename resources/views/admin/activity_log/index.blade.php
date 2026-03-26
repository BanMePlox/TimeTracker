@extends('layouts.admin')

@section('title', 'Log de actividad')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="dm-title text-2xl font-bold text-gray-800">Log de actividad</h3>
        <p class="dm-muted text-gray-500 text-sm mt-1">Registro inmutable de todas las acciones de administración</p>
    </div>
    <div class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-lg text-sm">
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
                    <option value="{{ $admin->id }}" {{ request('user_id') == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
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
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm font-medium transition-colors">Filtrar</button>
            <a href="{{ route('admin.activity-log.index') }}" class="dm-link-card bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 px-5 py-2 rounded-lg text-sm font-medium transition-colors">Limpiar</a>
        </div>
    </form>
</div>

@php
$colores = [
    'login'               => ['badge' => 'bg-blue-100 text-blue-700',    'dot' => 'bg-blue-500'],
    'logout'              => ['badge' => 'bg-gray-100 text-gray-600',    'dot' => 'bg-gray-400'],
    'usuario_creado'      => ['badge' => 'bg-green-100 text-green-700',  'dot' => 'bg-green-500'],
    'usuario_actualizado' => ['badge' => 'bg-yellow-100 text-yellow-700','dot' => 'bg-yellow-500'],
    'usuario_eliminado'   => ['badge' => 'bg-red-100 text-red-700',      'dot' => 'bg-red-500'],
    'pin_regenerado'      => ['badge' => 'bg-purple-100 text-purple-700','dot' => 'bg-purple-500'],
    'fichaje_corregido'   => ['badge' => 'bg-orange-100 text-orange-700','dot' => 'bg-orange-500'],
    'fichaje_eliminado'   => ['badge' => 'bg-red-100 text-red-700',      'dot' => 'bg-red-500'],
    'ausencia_creada'     => ['badge' => 'bg-green-100 text-green-700',  'dot' => 'bg-green-500'],
    'ausencia_actualizada'=> ['badge' => 'bg-yellow-100 text-yellow-700','dot' => 'bg-yellow-500'],
    'ausencia_eliminada'  => ['badge' => 'bg-red-100 text-red-700',      'dot' => 'bg-red-500'],
    'ausencia_aprobada'   => ['badge' => 'bg-emerald-100 text-emerald-700','dot' => 'bg-emerald-500'],
    'ausencia_rechazada'  => ['badge' => 'bg-red-100 text-red-700',      'dot' => 'bg-red-500'],
];
$logsPorDia = $logs->groupBy(fn($l) => $l->created_at->format('Y-m-d'));
@endphp

@if($logs->isEmpty())
<div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center dm-muted text-gray-400">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
    </svg>
    No hay registros que coincidan con los filtros.
</div>
@else

<div class="flex items-center justify-between mb-3 px-1">
    <span class="dm-muted text-xs text-gray-400">{{ $logs->total() }} {{ $logs->total() === 1 ? 'registro' : 'registros' }} · página {{ $logs->currentPage() }} de {{ $logs->lastPage() }}</span>
</div>

{{-- Timeline agrupado por día --}}
@foreach($logsPorDia as $fecha => $entradas)
@php $dia = \Carbon\Carbon::parse($fecha); @endphp

<div class="mb-6">
    {{-- Cabecera del día --}}
    <div class="flex items-center gap-3 mb-3">
        <div class="dm-card bg-white border border-gray-200 rounded-lg px-3 py-1.5 flex items-center gap-2 shadow-sm">
            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <span class="dm-title text-gray-700 text-sm font-semibold">
                {{ $dia->isToday() ? 'Hoy' : ($dia->isYesterday() ? 'Ayer' : ucfirst($dia->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY'))) }}
            </span>
        </div>
        <div class="flex-1 h-px bg-gray-100 dm-border"></div>
        <span class="dm-muted text-xs text-gray-400">{{ $entradas->count() }} {{ $entradas->count() === 1 ? 'evento' : 'eventos' }}</span>
    </div>

    {{-- Entradas del día --}}
    <div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        @foreach($entradas as $i => $log)
        @php
            $c = $colores[$log->accion] ?? ['badge' => 'bg-gray-100 text-gray-600', 'dot' => 'bg-gray-400'];
        @endphp
        <div class="flex items-start gap-4 px-5 py-4 {{ !$loop->last ? 'border-b border-gray-50 dm-border' : '' }} hover:bg-gray-50/50 dm-row transition-colors">

            {{-- Dot + línea --}}
            <div class="flex flex-col items-center pt-1 flex-shrink-0">
                <div class="w-2.5 h-2.5 rounded-full {{ $c['dot'] }} ring-2 ring-white"></div>
                @if(!$loop->last)
                <div class="w-px flex-1 bg-gray-100 mt-1.5 min-h-[1rem]"></div>
                @endif
            </div>

            {{-- Contenido --}}
            <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-1">
                    <span class="inline-flex px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $c['badge'] }}">
                        {{ str_replace('_', ' ', ucfirst($log->accion)) }}
                    </span>
                    @if($log->user)
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center text-white text-[10px] font-bold flex-shrink-0">
                            {{ strtoupper(substr($log->user->name, 0, 1)) }}
                        </div>
                        <span class="dm-title text-gray-700 text-sm font-medium">{{ $log->user->name }}</span>
                    </div>
                    @else
                    <span class="dm-muted text-gray-400 text-xs italic">Sistema</span>
                    @endif
                </div>
                <p class="dm-text text-gray-600 text-sm">{{ $log->descripcion }}</p>
            </div>

            {{-- Meta: hora + IP --}}
            <div class="flex-shrink-0 text-right">
                <p class="dm-title text-gray-700 font-mono text-sm font-medium">{{ $log->created_at->format('H:i:s') }}</p>
                @if($log->ip)
                <p class="dm-muted text-gray-400 font-mono text-xs mt-0.5">{{ $log->ip }}</p>
                @endif
            </div>

        </div>
        @endforeach
    </div>
</div>
@endforeach

{{-- Paginación --}}
@if($logs->hasPages())
<div class="mt-4">
    {{ $logs->withQueryString()->links() }}
</div>
@endif

@endif
@endsection
