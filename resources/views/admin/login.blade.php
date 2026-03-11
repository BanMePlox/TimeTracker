@extends('layouts.app')

@section('title', 'Acceso Administración')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-2xl mb-4 shadow-lg shadow-blue-500/30">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Panel de Administración</h1>
            <p class="text-slate-400 text-sm mt-1">Sistema de Fichajes</p>
        </div>

        <!-- Card -->
        <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl">
            @if($errors->any())
                <div class="mb-6 bg-red-500/20 border border-red-500/40 text-red-300 px-4 py-3 rounded-xl text-sm">
                    @foreach($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-slate-300 text-sm font-medium mb-2">Correo electrónico</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                           class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="admin@fichajes.com"
                           required autofocus>
                </div>

                <div>
                    <label for="password" class="block text-slate-300 text-sm font-medium mb-2">Contraseña</label>
                    <input type="password" id="password" name="password"
                           class="w-full bg-white/10 border border-white/20 rounded-xl px-4 py-3 text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all"
                           placeholder="••••••••"
                           required>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember" class="w-4 h-4 rounded border-white/30 bg-white/10 text-blue-500">
                    <label for="remember" class="ml-2 text-slate-300 text-sm">Recordarme</label>
                </div>

                <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-500 text-white font-semibold py-3 px-4 rounded-xl transition-colors shadow-lg shadow-blue-500/30 mt-2">
                    Iniciar sesión
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('fichaje.index') }}" class="text-slate-500 hover:text-slate-400 text-sm transition-colors">
                    Volver al terminal de fichajes
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
