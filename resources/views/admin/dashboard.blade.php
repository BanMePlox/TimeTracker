@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="dm-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="dm-muted text-gray-500 text-sm font-medium">Total Empleados</p>
                <p class="dm-title text-3xl font-bold text-gray-900 mt-1">{{ $totalUsuarios }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="dm-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="dm-muted text-gray-500 text-sm font-medium">Fichajes Hoy</p>
                <p class="dm-title text-3xl font-bold text-gray-900 mt-1">{{ $fichajesHoy }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="dm-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="dm-muted text-gray-500 text-sm font-medium">Presentes Ahora</p>
                <p class="dm-title text-3xl font-bold text-gray-900 mt-1">{{ $empleadosPresentes }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="dm-card bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between">
            <div>
                <p class="dm-muted text-gray-500 text-sm font-medium">Horas trabajadas hoy</p>
                <p class="dm-title text-3xl font-bold text-gray-900 mt-1">{{ $horasHoy }}h</p>
                <p class="dm-muted text-gray-400 text-sm">{{ now()->format('d/m/Y') }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Recent Fichajes -->
<div class="dm-card bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="dm-border p-6 border-b border-gray-100 flex items-center justify-between">
        <h3 class="dm-title text-lg font-semibold text-gray-900">Últimos fichajes</h3>
        <a href="{{ route('admin.fichajes.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
            Ver todos
        </a>
    </div>
    <div class="overflow-x-auto">
        @if($ultimosFichajes->isEmpty())
            <div class="p-12 text-center dm-muted text-gray-400">
                <svg class="w-12 h-12 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p>No hay fichajes registrados todavía.</p>
            </div>
        @else
        <table class="w-full">
            <thead>
                <tr class="dm-thead bg-gray-50">
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Empleado</th>
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Tipo</th>
                    <th class="dm-muted text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-3">Fecha y Hora</th>
                </tr>
            </thead>
            <tbody class="dm-divide divide-y divide-gray-50">
                @foreach($ultimosFichajes as $fichaje)
                <tr class="dm-row hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-semibold text-sm mr-3">
                                {{ substr($fichaje->user->name ?? 'D', 0, 1) }}
                            </div>
                            <span class="dm-title text-gray-900 font-medium">{{ $fichaje->user->name ?? 'Desconocido' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if($fichaje->tipo === 'entrada')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2"></span>
                                Entrada
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-orange-500 mr-2"></span>
                                Salida
                            </span>
                        @endif
                    </td>
                    <td class="dm-text px-6 py-4 text-gray-600 text-sm">
                        {{ $fichaje->created_at->format('d/m/Y H:i:s') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
    <a href="{{ route('admin.users.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white rounded-2xl p-6 flex items-center transition-colors group">
        <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-lg">Nuevo Empleado</p>
            <p class="text-blue-200 text-sm">Registrar un nuevo empleado en el sistema</p>
        </div>
    </a>

    <a href="{{ route('admin.fichajes.index') }}"
       class="dm-link-card bg-white hover:bg-gray-50 text-gray-900 rounded-2xl p-6 flex items-center border border-gray-200 transition-colors group">
        <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mr-4">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
        </div>
        <div>
            <p class="font-semibold text-lg">Ver Fichajes</p>
            <p class="dm-muted text-gray-500 text-sm">Consultar y filtrar todos los registros</p>
        </div>
    </a>
</div>
@endsection
