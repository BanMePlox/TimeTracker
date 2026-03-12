@extends('layouts.empleado')

@section('title', 'Mi panel')

@section('content')
<!-- Greeting -->
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-900">Hola, {{ explode(' ', $user->name)[0] }}</h2>
    <p class="text-gray-500 text-sm mt-0.5">{{ now()->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>
</div>

<!-- Status card -->
<div class="rounded-2xl p-6 mb-6 flex items-center gap-5 {{ $dentroAhora ? 'bg-green-500' : 'bg-slate-700' }} text-white shadow-sm">
    <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
        @if($dentroAhora)
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
            </svg>
        @else
            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
        @endif
    </div>
    <div>
        <p class="text-white/70 text-sm font-medium">Estado actual</p>
        <p class="text-2xl font-bold">{{ $dentroAhora ? 'Dentro' : 'Fuera' }}</p>
        @if($ultimoFichaje)
            <p class="text-white/70 text-sm">Último fichaje: {{ $ultimoFichaje->created_at->format('H:i') }} — {{ $ultimoFichaje->tipo }}</p>
        @endif
    </div>
    <div class="ml-auto text-right">
        <a href="{{ route('fichaje.index') }}" target="_blank"
           class="inline-flex items-center gap-2 bg-white/20 hover:bg-white/30 text-white text-sm font-medium px-4 py-2 rounded-xl transition-colors">
            Ir al terminal
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
            </svg>
        </a>
    </div>
</div>

<!-- Hours cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    @php
        $deficitSemana = round($esperadoSemana - $horasEstaSemana, 2);
        $deficitMes    = round($esperadoMes    - $horasEsteMes, 2);
    @endphp
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-sm">Horas esta semana</p>
        <p class="text-3xl font-bold mt-1 {{ $deficitSemana > 0 ? 'text-orange-500' : 'text-blue-600' }}">{{ $horasEstaSemana }}h</p>
        <p class="text-xs mt-1.5 {{ $deficitSemana > 0 ? 'text-orange-400' : 'text-green-500' }}">
            @if($deficitSemana > 0) Déficit: {{ $deficitSemana }}h (esperado {{ $esperadoSemana }}h)
            @else En objetivo (esperado {{ $esperadoSemana }}h) @endif
        </p>
    </div>
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
        <p class="text-gray-500 text-sm">Horas este mes</p>
        <p class="text-3xl font-bold mt-1 {{ $deficitMes > 0 ? 'text-orange-500' : 'text-green-600' }}">{{ $horasEsteMes }}h</p>
        <p class="text-xs mt-1.5 {{ $deficitMes > 0 ? 'text-orange-400' : 'text-green-500' }}">
            @if($deficitMes > 0) Déficit: {{ $deficitMes }}h (esperado {{ $esperadoMes }}h)
            @else En objetivo (esperado {{ $esperadoMes }}h) @endif
        </p>
    </div>
</div>

<!-- Quick links -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <a href="{{ route('empleado.fichajes.index') }}"
       class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 group">
        <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-gray-900">Mis fichajes</p>
            <p class="text-gray-400 text-sm">Consultar historial</p>
        </div>
    </a>
    <a href="{{ route('empleado.ausencias.create') }}"
       class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition-shadow flex items-center gap-4 group">
        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center group-hover:bg-purple-200 transition-colors">
            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-gray-900">Solicitar ausencia</p>
            <p class="text-gray-400 text-sm">
                @if($ausenciasPendientes > 0)
                    {{ $ausenciasPendientes }} pendiente{{ $ausenciasPendientes > 1 ? 's' : '' }} de aprobación
                @else
                    Vacaciones, bajas y más
                @endif
            </p>
        </div>
    </a>
</div>

<!-- Recent fichajes -->
@if($ultimosFichajes->isNotEmpty())
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-900">Últimos fichajes</h3>
        <a href="{{ route('empleado.fichajes.index') }}" class="text-blue-500 hover:text-blue-600 text-sm">Ver todos</a>
    </div>
    <div class="divide-y divide-gray-50">
        @foreach($ultimosFichajes as $f)
        <div class="px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $f->tipo === 'entrada' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ ucfirst($f->tipo) }}
                </span>
            </div>
            <span class="text-gray-500 text-sm font-mono">{{ $f->created_at->format('d/m/Y H:i') }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif
@endsection
