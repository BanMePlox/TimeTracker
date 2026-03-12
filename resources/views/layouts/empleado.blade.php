<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Portal') - TimeTrack</title>
    <link rel="icon" type="image/png" href="/Logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- Top nav -->
    <nav class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 flex items-center justify-between h-14">
            <div class="flex items-center gap-6">
                <a href="{{ route('empleado.dashboard') }}" class="text-lg font-bold">
                    <span class="text-blue-500">Time</span><span class="text-green-500">Track</span>
                </a>
                <div class="hidden sm:flex items-center gap-1 text-sm">
                    <a href="{{ route('empleado.dashboard') }}"
                       class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ request()->routeIs('empleado.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Inicio
                    </a>
                    <a href="{{ route('empleado.fichajes.index') }}"
                       class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ request()->routeIs('empleado.fichajes.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Mis fichajes
                    </a>
                    <a href="{{ route('empleado.ausencias.index') }}"
                       class="px-3 py-1.5 rounded-lg font-medium transition-colors {{ request()->routeIs('empleado.ausencias.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Mis ausencias
                    </a>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-bold">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <span class="text-sm text-gray-700 hidden sm:block">{{ auth()->user()->name }}</span>
                <form action="{{ route('empleado.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-gray-600 text-sm transition-colors">
                        Salir
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Page content -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl flex items-center gap-2">
                <svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    @stack('scripts')
</body>
</html>
