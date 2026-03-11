@extends('layouts.admin')

@section('title', 'Presencia actual')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h3 class="text-2xl font-bold text-gray-800">Presencia actual</h3>
        <p class="text-gray-500 text-sm mt-1">Actualizado a las <span id="hora-actualizacion"></span></p>
    </div>
    <div class="flex items-center gap-4">
        <div class="bg-green-100 text-green-800 px-4 py-2 rounded-full font-semibold text-sm">
            {{ $presentes->count() }} dentro
        </div>
        <div class="bg-gray-100 text-gray-600 px-4 py-2 rounded-full font-semibold text-sm">
            {{ $ausentes->count() }} fuera
        </div>
        <button onclick="window.location.reload()" class="flex items-center gap-2 bg-white border border-gray-200 px-4 py-2 rounded-lg text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Actualizar
        </button>
    </div>
</div>

{{-- Empleados presentes --}}
<div class="mb-8">
    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
        <span class="w-2 h-2 bg-green-500 rounded-full inline-block animate-pulse"></span>
        Dentro ahora ({{ $presentes->count() }})
    </h4>

    @if($presentes->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
            Ningún empleado ha fichado entrada hoy.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($presentes as $empleado)
            <div class="bg-white rounded-xl border border-green-200 shadow-sm p-5 flex flex-col gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($empleado->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-800 truncate">{{ $empleado->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $empleado->email }}</p>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Entrada</span>
                    <span class="font-medium text-gray-700">{{ $empleado->entrada_at->format('H:i') }}</span>
                </div>
                <div class="flex items-center justify-between text-sm">
                    <span class="text-gray-500">Tiempo dentro</span>
                    <span class="font-semibold text-green-600 tiempo-transcurrido"
                          data-desde="{{ $empleado->entrada_at->timestamp }}">
                        —
                    </span>
                </div>
                <a href="{{ route('admin.users.show', $empleado) }}"
                   class="mt-1 text-center text-xs text-blue-600 hover:text-blue-800 hover:underline">
                    Ver historial
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Empleados fuera --}}
<div>
    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3 flex items-center gap-2">
        <span class="w-2 h-2 bg-gray-400 rounded-full inline-block"></span>
        Fuera ({{ $ausentes->count() }})
    </h4>

    @if($ausentes->isEmpty())
        <div class="bg-white rounded-xl border border-gray-200 p-8 text-center text-gray-400">
            Todos los empleados están dentro.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($ausentes as $empleado)
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 flex flex-col gap-3 opacity-70">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center text-white font-bold text-lg flex-shrink-0">
                        {{ strtoupper(substr($empleado->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-700 truncate">{{ $empleado->name }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $empleado->email }}</p>
                    </div>
                </div>
                <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-sm">
                    <span class="text-gray-500">Última salida</span>
                    <span class="font-medium text-gray-500">
                        {{ $empleado->salida_at ? $empleado->salida_at->format('H:i') : 'Sin fichajes' }}
                    </span>
                </div>
                <a href="{{ route('admin.users.show', $empleado) }}"
                   class="mt-1 text-center text-xs text-blue-600 hover:text-blue-800 hover:underline">
                    Ver historial
                </a>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Mostrar hora de actualización
    function pad(n) { return String(n).padStart(2, '0'); }

    function horaActual() {
        const now = new Date();
        return `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    }

    document.getElementById('hora-actualizacion').textContent = horaActual();

    // Calcular tiempo transcurrido dinámicamente
    function actualizarTiempos() {
        const now = Math.floor(Date.now() / 1000);
        document.querySelectorAll('.tiempo-transcurrido').forEach(el => {
            const desde = parseInt(el.dataset.desde);
            const diff = now - desde;
            const h = Math.floor(diff / 3600);
            const m = Math.floor((diff % 3600) / 60);
            const s = diff % 60;
            el.textContent = h > 0
                ? `${h}h ${pad(m)}m`
                : `${pad(m)}m ${pad(s)}s`;
        });
    }

    actualizarTiempos();
    setInterval(actualizarTiempos, 1000);

    // Auto-refrescar la página cada 60 segundos
    setTimeout(() => window.location.reload(), 60000);
</script>
@endpush
