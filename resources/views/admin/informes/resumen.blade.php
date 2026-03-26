@extends('layouts.admin')

@section('title', 'Resumen mensual')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="dm-title text-2xl font-bold text-gray-900">Resumen mensual</h2>
        <p class="dm-muted text-gray-500 text-sm mt-1">{{ ucfirst($mesNombre) }} {{ $anio }} · horas trabajadas vs esperadas</p>
    </div>
    <a href="{{ route('admin.informes.pdf', ['mes' => $mes, 'anio' => $anio]) }}"
       target="_blank"
       class="flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
        </svg>
        Exportar PDF
    </a>
</div>

<!-- Month selector -->
<div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Mes</label>
            <select name="mes" class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                @foreach($meses as $num => $nombre)
                    <option value="{{ $num }}" {{ $mes == $num ? 'selected' : '' }}>{{ ucfirst($nombre) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1.5">Año</label>
            <select name="anio" class="border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white text-gray-900">
                @for($y = now()->year; $y >= now()->year - 3; $y--)
                    <option value="{{ $y }}" {{ $anio == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <button type="submit"
                class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
            Ver
        </button>
    </form>
</div>

<!-- Summary table -->
<div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    @if(empty($resumen))
        <div class="p-12 text-center dm-muted text-gray-400">
            No hay datos para este período.
        </div>
    @else
    <table class="w-full">
        <thead>
            <tr class="dm-thead bg-gray-50 border-b border-gray-100">
                <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Empleado</th>
                <th class="dm-muted text-center text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Días trabajados</th>
                <th class="dm-muted text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Horas trabajadas</th>
                <th class="dm-muted text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Horas esperadas</th>
                <th class="dm-muted text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Diferencia</th>
                <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Progreso</th>
            </tr>
        </thead>
        <tbody class="dm-divide divide-y divide-gray-50">
            @foreach($resumen as $row)
            @php
                $pct = $row['horas_esperadas'] > 0
                    ? min(100, round(($row['horas_trabajadas'] / $row['horas_esperadas']) * 100))
                    : 0;
                $barColor = $row['diferencia'] >= 0 ? 'bg-emerald-400' : 'bg-orange-400';
                $diffColor = $row['diferencia'] >= 0 ? 'text-emerald-600' : 'text-orange-500';
                $diffPrefix = $row['diferencia'] >= 0 ? '+' : '';
            @endphp
            <tr class="dm-row hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm flex-shrink-0">
                            {{ substr($row['usuario']->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="dm-title text-gray-900 font-medium text-sm">{{ $row['usuario']->name }}</p>
                            <p class="dm-muted text-gray-400 text-xs">{{ $row['usuario']->horas_diarias }}h/día</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-center">
                    <span class="dm-title text-gray-700 font-semibold text-sm">{{ $row['dias_trabajados'] }}</span>
                    <span class="dm-muted text-gray-400 text-xs"> / {{ $row['dias_laborables'] }}</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="dm-title text-gray-900 font-bold">{{ $row['horas_trabajadas'] }}h</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="dm-muted text-gray-500">{{ $row['horas_esperadas'] }}h</span>
                </td>
                <td class="px-6 py-4 text-right">
                    <span class="font-semibold text-sm {{ $diffColor }}">
                        {{ $diffPrefix }}{{ $row['diferencia'] }}h
                    </span>
                </td>
                <td class="px-6 py-4 w-40">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full {{ $barColor }} transition-all" style="width: {{ $pct }}%"></div>
                        </div>
                        <span class="dm-muted text-gray-400 text-xs w-8 text-right">{{ $pct }}%</span>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

@endsection
