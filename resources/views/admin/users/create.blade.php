@extends('layouts.admin')

@section('title', 'Nuevo Empleado')

@section('content')
<div class="max-w-2xl">
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center text-gray-500 hover:text-gray-700 text-sm transition-colors">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver a empleados
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <h3 class="text-lg font-semibold text-gray-900">Registrar nuevo empleado</h3>
            <p class="text-gray-500 text-sm mt-1">Se generará un PIN automáticamente</p>
        </div>

        <!-- PIN Preview -->
        <div class="mx-6 mt-6 p-5 bg-blue-50 border-2 border-blue-200 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-blue-600 text-sm font-semibold uppercase tracking-wider">PIN asignado</p>
                <p class="text-4xl font-bold text-blue-800 font-mono tracking-widest mt-1" id="pin-preview">{{ $pin }}</p>
                <p class="text-blue-500 text-xs mt-1">Entrégalo al empleado de forma segura</p>
            </div>
            <div class="flex flex-col gap-2">
                <button type="button" onclick="regenerarPin()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Regenerar PIN
                </button>
            </div>
        </div>

        <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-5">
            @csrf

            <input type="hidden" name="pin" id="pin-field" value="{{ $pin }}">

            @if($errors->any())
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">Nombre completo *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           placeholder="Juan García López"
                           required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           placeholder="juan@empresa.com"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Contraseña *</label>
                    <input type="password" id="password" name="password"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           placeholder="Mínimo 6 caracteres"
                           required>
                    <p class="text-gray-400 text-xs mt-1">Necesaria para acceder al panel de admin</p>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Rol *</label>
                    <select id="role" name="role"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white">
                        <option value="empleado" {{ old('role') !== 'admin' ? 'selected' : '' }}>Empleado</option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="horas_diarias" class="block text-sm font-medium text-gray-700 mb-1.5">Horas diarias *</label>
                <input type="number" id="horas_diarias" name="horas_diarias" value="{{ old('horas_diarias', 8) }}"
                       class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                       min="1" max="24" step="0.5" required>
                <p class="text-gray-400 text-xs mt-1">Jornada laboral esperada por día (para cálculo de déficit)</p>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 text-gray-700 hover:text-gray-900 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                    Crear Empleado
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function regenerarPin() {
        // Generate a random 4-digit PIN on the client side
        const pin = String(Math.floor(Math.random() * 10000)).padStart(4, '0');
        document.getElementById('pin-preview').textContent = pin;
        document.getElementById('pin-field').value = pin;
    }
</script>
@endpush
