@extends('layouts.admin')

@section('title', 'Calendario de ausencias')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-slate-100">Calendario de ausencias</h2>
        <p class="text-gray-400 text-sm mt-0.5">Ausencias aprobadas y festivos del mes</p>
    </div>
    <a href="{{ route('admin.ausencias.index') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-gray-700 dark:text-slate-200 font-medium rounded-xl text-sm transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
        </svg>
        Ver lista
    </a>
</div>

<!-- Month navigation -->
<div class="flex items-center justify-between mb-4">
    <a href="{{ route('admin.ausencias.calendario', ['mes' => $prevMes->month, 'anio' => $prevMes->year]) }}"
       class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        {{ ucfirst($prevMes->locale('es')->isoFormat('MMMM YYYY')) }}
    </a>

    <h3 class="text-xl font-bold text-gray-900 dark:text-slate-100">
        {{ ucfirst($inicio->locale('es')->isoFormat('MMMM [de] YYYY')) }}
    </h3>

    <a href="{{ route('admin.ausencias.calendario', ['mes' => $nextMes->month, 'anio' => $nextMes->year]) }}"
       class="flex items-center gap-1 px-3 py-2 text-sm text-gray-600 dark:text-slate-300 hover:bg-gray-100 dark:hover:bg-slate-700 rounded-lg transition-colors">
        {{ ucfirst($nextMes->locale('es')->isoFormat('MMMM YYYY')) }}
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>

<!-- Legend -->
<div class="flex flex-wrap gap-3 mb-4 text-xs">
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-blue-400 inline-block"></span> Vacaciones</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-red-400 inline-block"></span> Baja médica</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-amber-400 inline-block"></span> Ausencia justificada</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-gray-400 inline-block"></span> Ausencia injustificada</span>
    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-rose-200 border border-rose-400 inline-block"></span> Festivo</span>
</div>

<!-- Calendar grid -->
<div class="bg-white dm-card rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <!-- Day headers -->
    <div class="grid grid-cols-7 border-b border-gray-100 dm-border">
        @foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $diaNombre)
        <div class="px-3 py-2 text-center text-xs font-semibold text-gray-500 dm-muted
            {{ in_array($diaNombre, ['Sáb','Dom']) ? 'bg-gray-50 dm-thead' : '' }}">
            {{ $diaNombre }}
        </div>
        @endforeach
    </div>

    @php
        // Find the weekday of the first day (Monday=1, Sunday=7 in ISO)
        $primerDia = array_key_first($dias);
        $primerDiaSemana = \Carbon\Carbon::parse($primerDia)->isoFormat('E'); // 1=Mon..7=Sun
        $celdas = array_merge(array_fill(0, $primerDiaSemana - 1, null), array_values($dias));
        $filas = array_chunk($celdas, 7);
    @endphp

    @foreach($filas as $fila)
    <div class="grid grid-cols-7 border-b border-gray-50 dm-border last:border-b-0">
        @foreach($fila as $celda)
        @php
            $esFinSemana = $celda && in_array($celda['diaSemana'], [0, 6]);
            $esFestivo   = $celda && $celda['festivo'];
            $esHoy       = $celda && \Carbon\Carbon::today()->day === $celda['numero']
                           && $mes == now()->month && $anio == now()->year;
        @endphp
        <div class="min-h-[90px] p-2 border-r border-gray-50 dm-border last:border-r-0
            {{ $celda === null ? 'bg-gray-50/50 dm-thead' : '' }}
            {{ $esFinSemana && $celda ? 'bg-gray-50/70 dark:bg-slate-800/50' : '' }}
            {{ $esFestivo ? 'bg-rose-50/70 dark:bg-rose-900/10' : '' }}
        ">
            @if($celda !== null)
            <div class="flex items-center justify-between mb-1">
                <span class="text-sm font-medium {{ $esHoy ? 'w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs' : 'text-gray-700 dm-text' }}">
                    {{ $celda['numero'] }}
                </span>
                @if($esFestivo)
                    <span class="text-xs text-rose-500 dark:text-rose-400 truncate ml-1 max-w-[70%]" title="{{ $celda['festivo']->nombre }}">
                        🎉 {{ Str::limit($celda['festivo']->nombre, 12) }}
                    </span>
                @endif
            </div>

            @foreach($celda['ausencias'] as $ausencia)
            @php
                $color = match($ausencia->tipo) {
                    'vacaciones'            => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300',
                    'baja_medica'           => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
                    'ausencia_justificada'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
                    'ausencia_injustificada'=> 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300',
                    default                 => 'bg-purple-100 text-purple-700',
                };
            @endphp
            <div class="text-xs px-1.5 py-0.5 rounded mb-0.5 truncate {{ $color }}" title="{{ $ausencia->user->name }} - {{ $ausencia->tipo_label }}">
                {{ explode(' ', $ausencia->user->name)[0] }}
            </div>
            @endforeach
            @endif
        </div>
        @endforeach
    </div>
    @endforeach
</div>
@endsection
