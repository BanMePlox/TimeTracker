@extends('layouts.admin')

@section('title', 'Detalle de Empleado')

@section('content')
<div class="max-w-4xl">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a empleados
        </a>
    </div>

    <!-- User Info Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 flex items-center justify-between">
        <div class="flex items-center">
            @if($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                     class="w-16 h-16 rounded-2xl object-cover mr-5 border border-gray-200">
            @else
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-white font-bold text-2xl mr-5 {{ $user->role === 'admin' ? 'bg-purple-500' : 'bg-blue-500' }}">
                    {{ substr($user->name, 0, 1) }}
                </div>
            @endif
            <div>
                <h3 class="text-xl font-bold text-gray-900">{{ $user->name }}</h3>
                <p class="text-gray-500">{{ $user->email }}</p>
                <div class="flex items-center gap-3 mt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-gray-100 text-gray-700 font-mono text-sm font-bold tracking-widest">
                        PIN: {{ $user->pin }}
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-medium">
                        {{ $user->horas_diarias }}h/día
                    </span>
                </div>
            </div>
        </div>
        <a href="{{ route('admin.users.edit', $user) }}"
           class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-xl text-sm transition-colors">
            Editar
        </a>
    </div>

    <!-- Hours Summary Cards -->
    @php
        $diasSemana = max(now()->dayOfWeek ?: 7, 1);
        $diasMes = now()->day;
        $esperadoSemana = round($user->horas_diarias * $diasSemana, 2);
        $esperadoMes = round($user->horas_diarias * $diasMes, 2);
        $deficitSemana = round($esperadoSemana - $horasEstaSemana, 2);
        $deficitMes = round($esperadoMes - $horasEsteMes, 2);
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Horas esta semana</p>
            <p class="text-3xl font-bold {{ $deficitSemana > 0 ? 'text-orange-500' : 'text-blue-600' }} mt-1">{{ $horasEstaSemana }}h</p>
            <p class="text-xs mt-2 {{ $deficitSemana > 0 ? 'text-orange-400' : 'text-green-500' }}">
                @if($deficitSemana > 0)
                    Déficit: {{ $deficitSemana }}h (esperado {{ $esperadoSemana }}h)
                @else
                    En objetivo (esperado {{ $esperadoSemana }}h)
                @endif
            </p>
        </div>
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <p class="text-gray-500 text-sm font-medium">Horas este mes</p>
            <p class="text-3xl font-bold {{ $deficitMes > 0 ? 'text-orange-500' : 'text-green-600' }} mt-1">{{ $horasEsteMes }}h</p>
            <p class="text-xs mt-2 {{ $deficitMes > 0 ? 'text-orange-400' : 'text-green-500' }}">
                @if($deficitMes > 0)
                    Déficit: {{ $deficitMes }}h (esperado {{ $esperadoMes }}h)
                @else
                    En objetivo (esperado {{ $esperadoMes }}h)
                @endif
            </p>
        </div>
    </div>

    <!-- QR Code Card -->
    @if($user->role === 'empleado')
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-6 flex items-center gap-8">
        <div class="flex-1">
            <p class="text-gray-800 font-semibold mb-1">Código QR de fichaje</p>
            <p class="text-gray-400 text-sm mb-4">Escanea este código en el terminal para fichar automáticamente sin introducir el PIN.</p>
            <button onclick="imprimirQR()" class="inline-flex items-center gap-2 text-sm px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Imprimir QR
            </button>
        </div>
        <div class="flex flex-col items-center gap-2">
            <div id="qrcode" class="p-2 bg-white border border-gray-200 rounded-xl"></div>
            <p class="text-xs text-gray-400">{{ $user->name }}</p>
        </div>
    </div>
    @endif

    <!-- Hours per day (last 30 days) -->
    @if(!empty($horasPorDia))
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-100">
            <h4 class="text-lg font-semibold text-gray-900">Horas trabajadas (últimos 30 días)</h4>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Fecha</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Entrada</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Salida</th>
                        <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Horas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($horasPorDia as $fecha => $dia)
                        @foreach($dia['sesiones'] as $i => $sesion)
                        <tr class="hover:bg-gray-50 transition-colors">
                            @if($i === 0)
                            <td class="px-6 py-3 text-gray-700 text-sm font-medium" rowspan="{{ count($dia['sesiones']) }}">
                                {{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}
                                @if(count($dia['sesiones']) > 1 || $dia['total_minutos'] > 0)
                                <br><span class="text-xs text-blue-600 font-semibold">Total: {{ $dia['total_horas'] }}h</span>
                                @endif
                            </td>
                            @endif
                            <td class="px-6 py-3 text-gray-700 font-mono text-sm">
                                {{ $sesion['entrada']->created_at->format('H:i:s') }}
                            </td>
                            <td class="px-6 py-3 text-gray-700 font-mono text-sm">
                                @if($sesion['salida'])
                                    {{ $sesion['salida']->created_at->format('H:i:s') }}
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        En curso
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm">
                                @if($sesion['horas'] !== null)
                                    @php $color = $sesion['horas'] >= $user->horas_diarias ? 'text-green-600' : 'text-orange-500'; @endphp
                                    <span class="font-semibold {{ $color }}">{{ $sesion['horas'] }}h</span>
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Fichajes Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h4 class="text-lg font-semibold text-gray-900">Historial de fichajes</h4>
            <p class="text-gray-500 text-sm">{{ $fichajes->total() }} fichajes en total</p>
        </div>

        @if($fichajes->isEmpty())
            <div class="p-12 text-center text-gray-400">
                <p>Este empleado no tiene fichajes registrados.</p>
            </div>
        @else
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Tipo</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Fecha</th>
                    <th class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Hora</th>
                    <th class="text-right text-xs font-semibold text-gray-500 uppercase tracking-wider px-6 py-4">Acción</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($fichajes as $fichaje)
                <tr class="hover:bg-gray-50 transition-colors">
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
                    <td class="px-6 py-4 text-gray-700">{{ $fichaje->created_at->format('d/m/Y') }}</td>
                    <td class="px-6 py-4 text-gray-700 font-mono">{{ $fichaje->created_at->format('H:i:s') }}</td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('admin.fichajes.destroy', $fichaje) }}" method="POST"
                              onsubmit="return confirm('¿Eliminar este fichaje?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 text-xs transition-colors">
                                Eliminar
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @if($fichajes->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $fichajes->links() }}
        </div>
        @endif
        @endif
    </div>
</div>
@endsection

@push('scripts')
@if($user->role === 'empleado')
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
    const qrUrl = "{{ url('/fichaje') }}?pin={{ $user->pin }}";
    new QRCode(document.getElementById('qrcode'), {
        text: qrUrl,
        width: 128,
        height: 128,
        colorDark: '#1e293b',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.M,
    });

    function imprimirQR() {
        const w = window.open('', '_blank', 'width=400,height=500');
        const canvas = document.querySelector('#qrcode canvas');
        const img = canvas ? canvas.toDataURL() : '';
        w.document.write(`
            <html><head><title>QR - {{ $user->name }}</title>
            <style>body{font-family:sans-serif;text-align:center;padding:40px}h2{font-size:18px}p{color:#666;font-size:14px}</style>
            </head><body>
            <h2>{{ $user->name }}</h2>
            <p>Terminal de fichaje · TimeTrack</p>
            <img src="${img}" style="margin:20px auto;display:block">
            <p>Escanea para fichar</p>
            <script>window.onload=()=>window.print()<\/script>
            </body></html>
        `);
        w.document.close();
    }
</script>
@endif
@endpush
