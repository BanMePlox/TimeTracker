@extends('layouts.admin')

@section('title', 'Editar Empleado')

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
        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-green-50 to-emerald-50">
            <h3 class="text-lg font-semibold text-gray-900">Editar empleado</h3>
            <p class="text-gray-500 text-sm mt-1">{{ $user->name }}</p>
        </div>

        <!-- PIN Section -->
        <div class="mx-6 mt-6 p-5 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm font-semibold uppercase tracking-wider">PIN actual</p>
                <p class="text-4xl font-bold text-gray-800 font-mono tracking-widest mt-1" id="pin-preview">{{ old('pin', $user->pin) }}</p>
            </div>
            <div class="flex flex-col gap-2">
                <form action="{{ route('admin.users.regenerar-pin', $user) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-white text-sm font-medium rounded-lg transition-colors"
                            onclick="return confirm('¿Regenerar el PIN de {{ addslashes($user->name) }}?')">
                        Regenerar PIN
                    </button>
                </form>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')

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
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">Correo electrónico *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="pin" class="block text-sm font-medium text-gray-700 mb-1.5">PIN (4 dígitos) *</label>
                    <input type="text" id="pin" name="pin" value="{{ old('pin', $user->pin) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 font-mono text-xl tracking-widest"
                           maxlength="4" pattern="[0-9]{4}"
                           required>
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1.5">Rol *</label>
                    <select id="role" name="role"
                            class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900 bg-white">
                        <option value="empleado" {{ old('role', $user->role) === 'empleado' ? 'selected' : '' }}>Empleado</option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrador</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">Nueva contraseña</label>
                    <input type="password" id="password" name="password"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           placeholder="Dejar en blanco para mantener la actual">
                    <p class="text-gray-400 text-xs mt-1">Solo necesaria para acceso al panel de administración</p>
                </div>

                <div>
                    <label for="horas_diarias" class="block text-sm font-medium text-gray-700 mb-1.5">Horas diarias *</label>
                    <input type="number" id="horas_diarias" name="horas_diarias" value="{{ old('horas_diarias', $user->horas_diarias) }}"
                           class="w-full border border-gray-300 rounded-xl px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-gray-900"
                           min="1" max="24" step="0.5" required>
                    <p class="text-gray-400 text-xs mt-1">Jornada laboral esperada por día</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}"
                   class="px-5 py-2.5 text-gray-700 hover:text-gray-900 font-medium transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition-colors shadow-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
