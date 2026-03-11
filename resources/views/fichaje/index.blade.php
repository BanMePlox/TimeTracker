@extends('layouts.app')

@section('title', 'Terminal de Fichajes')

@push('styles')
<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes pulse-success {
        0%, 100% { background-color: rgb(22, 163, 74); }
        50% { background-color: rgb(21, 128, 61); }
    }
    @keyframes pulse-error {
        0%, 100% { background-color: rgb(220, 38, 38); }
        50% { background-color: rgb(185, 28, 28); }
    }
    .fade-in-up { animation: fadeInUp 0.4s ease-out; }
    .key-btn:active { transform: scale(0.92); }
    .key-btn { transition: all 0.1s ease; }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-950 to-slate-900 flex flex-col items-center justify-center p-4">

    <!-- Header -->
    <div class="text-center mb-8 fade-in-up">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-500 rounded-2xl mb-4 shadow-lg shadow-blue-500/30">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
        <h1 class="text-3xl font-bold tracking-tight">
            <span class="text-blue-400">Time</span><span class="text-green-400">Track</span>
        </h1>
        <p id="reloj" class="text-blue-300 text-lg mt-2 font-mono"></p>
        <p id="fecha" class="text-slate-400 text-sm"></p>
    </div>

    <!-- Result Screen (shown after fichaje) -->
    @if(session('success'))
    <div id="resultado" class="fixed inset-0 flex items-center justify-center z-50 {{ session('tipo') === 'entrada' ? 'bg-green-600' : 'bg-orange-500' }} fade-in-up">
        <div class="text-center text-white p-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white/20 mb-6">
                @if(session('tipo') === 'entrada')
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                @else
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                @endif
            </div>
            <h2 class="text-4xl font-bold mb-2">{{ session('tipo') === 'entrada' ? 'ENTRADA' : 'SALIDA' }}</h2>
            <p class="text-2xl font-semibold mb-4">{{ session('nombre') }}</p>
            <p class="text-white/80 text-lg">{{ session('success') }}</p>
            <div class="mt-6">
                <p class="text-white/60 text-sm">Cerrando en <span id="countdown">3</span> segundos...</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div id="error-screen" class="fixed inset-0 flex items-center justify-center z-50 bg-red-600 fade-in-up">
        <div class="text-center text-white p-8">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-white/20 mb-6">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <h2 class="text-4xl font-bold mb-4">PIN INCORRECTO</h2>
            <p class="text-white/80 text-lg">{{ session('error') }}</p>
            <div class="mt-6">
                <p class="text-white/60 text-sm">Cerrando en <span id="countdown-error">3</span> segundos...</p>
            </div>
        </div>
    </div>
    @endif

    @if(session('bloqueado'))
    <div id="bloqueo-screen" class="fixed inset-0 flex items-center justify-center z-50 bg-gray-900 fade-in-up">
        <div class="text-center text-white p-8 max-w-sm">
            <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-red-500/20 mb-6 border-4 border-red-500">
                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="text-3xl font-bold mb-2 text-red-400">TERMINAL BLOQUEADO</h2>
            <p class="text-gray-400 mb-6">Demasiados intentos fallidos.</p>
            <div class="bg-white/5 rounded-2xl px-8 py-5 inline-block">
                <p class="text-gray-400 text-sm mb-1">Tiempo restante</p>
                <p id="bloqueo-countdown" class="text-5xl font-mono font-bold text-white tabular-nums">
                    {{ session('restantes') >= 60
                        ? floor(session('restantes') / 60) . ':' . str_pad(session('restantes') % 60, 2, '0', STR_PAD_LEFT)
                        : '0:' . str_pad(session('restantes'), 2, '0', STR_PAD_LEFT) }}
                </p>
            </div>
            <p class="text-gray-600 text-xs mt-6">El terminal se desbloqueará automáticamente.</p>
        </div>
    </div>
    @endif

    <!-- PIN Card -->
    <div class="bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 w-full max-w-sm shadow-2xl fade-in-up">
        <p class="text-center text-slate-300 text-sm mb-6 font-medium tracking-widest uppercase">Introduce tu PIN</p>

        <!-- PIN Display -->
        <div class="flex justify-center gap-3 mb-8" id="pin-display">
            <div class="pin-dot w-14 h-14 rounded-xl border-2 border-white/30 bg-white/5 flex items-center justify-center text-white text-2xl font-bold" data-index="0">
                <span class="dot-indicator w-3 h-3 rounded-full bg-white/30"></span>
            </div>
            <div class="pin-dot w-14 h-14 rounded-xl border-2 border-white/30 bg-white/5 flex items-center justify-center text-white text-2xl font-bold" data-index="1">
                <span class="dot-indicator w-3 h-3 rounded-full bg-white/30"></span>
            </div>
            <div class="pin-dot w-14 h-14 rounded-xl border-2 border-white/30 bg-white/5 flex items-center justify-center text-white text-2xl font-bold" data-index="2">
                <span class="dot-indicator w-3 h-3 rounded-full bg-white/30"></span>
            </div>
            <div class="pin-dot w-14 h-14 rounded-xl border-2 border-white/30 bg-white/5 flex items-center justify-center text-white text-2xl font-bold" data-index="3">
                <span class="dot-indicator w-3 h-3 rounded-full bg-white/30"></span>
            </div>
        </div>

        <!-- Hidden form -->
        <form id="fichaje-form" action="{{ route('fichaje.store') }}" method="POST">
            @csrf
            <input type="hidden" name="pin" id="pin-input" value="">
        </form>

        <!-- Numeric Keypad -->
        <div class="grid grid-cols-3 gap-3">
            @foreach(['1','2','3','4','5','6','7','8','9'] as $digit)
            <button onclick="addDigit('{{ $digit }}')"
                    class="key-btn h-16 bg-white/10 hover:bg-white/25 border border-white/20 rounded-2xl text-white text-2xl font-semibold shadow-sm">
                {{ $digit }}
            </button>
            @endforeach

            <!-- Delete -->
            <button onclick="deleteDigit()"
                    class="key-btn h-16 bg-red-500/20 hover:bg-red-500/40 border border-red-500/30 rounded-2xl text-red-300 flex items-center justify-center shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"/>
                </svg>
            </button>

            <!-- 0 -->
            <button onclick="addDigit('0')"
                    class="key-btn h-16 bg-white/10 hover:bg-white/25 border border-white/20 rounded-2xl text-white text-2xl font-semibold shadow-sm">
                0
            </button>

            <!-- Submit -->
            <button onclick="submitPin()"
                    class="key-btn h-16 bg-blue-600 hover:bg-blue-500 border border-blue-500 rounded-2xl text-white flex items-center justify-center shadow-sm shadow-blue-500/30">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </button>
        </div>

        <!-- Admin link -->
        <div class="mt-8 text-center">
            <a href="{{ route('admin.login') }}" class="text-slate-500 hover:text-slate-400 text-xs transition-colors">
                Acceso administración
            </a>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let pin = [];
    const MAX_LENGTH = 4;

    // Clock
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('reloj').textContent = `${hours}:${minutes}:${seconds}`;

        const days = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        const months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
        document.getElementById('fecha').textContent = `${days[now.getDay()]}, ${now.getDate()} de ${months[now.getMonth()]} de ${now.getFullYear()}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    function updateDisplay() {
        const dots = document.querySelectorAll('.pin-dot');
        dots.forEach((dot, i) => {
            dot.innerHTML = '';
            if (i < pin.length) {
                dot.classList.remove('border-white/30', 'bg-white/5');
                dot.classList.add('border-blue-400', 'bg-blue-500/20');
                const bullet = document.createElement('div');
                bullet.className = 'w-4 h-4 rounded-full bg-blue-400';
                dot.appendChild(bullet);
            } else {
                dot.classList.remove('border-blue-400', 'bg-blue-500/20');
                dot.classList.add('border-white/30', 'bg-white/5');
                const indicator = document.createElement('span');
                indicator.className = 'dot-indicator w-3 h-3 rounded-full bg-white/30';
                dot.appendChild(indicator);
            }
        });
    }

    function addDigit(digit) {
        if (pin.length < MAX_LENGTH) {
            pin.push(digit);
            updateDisplay();

            // Auto-submit when 4 digits entered
            if (pin.length === MAX_LENGTH) {
                setTimeout(() => submitPin(), 300);
            }
        }
    }

    function deleteDigit() {
        if (pin.length > 0) {
            pin.pop();
            updateDisplay();
        }
    }

    function submitPin() {
        if (pin.length === MAX_LENGTH) {
            document.getElementById('pin-input').value = pin.join('');
            document.getElementById('fichaje-form').submit();
        }
    }

    // Keyboard support
    document.addEventListener('keydown', function(e) {
        if (e.key >= '0' && e.key <= '9') {
            addDigit(e.key);
        } else if (e.key === 'Backspace') {
            deleteDigit();
        } else if (e.key === 'Enter') {
            submitPin();
        }
    });

    // Auto-close result screen
    @if(session('success'))
    let countdown = 3;
    const countdownEl = document.getElementById('countdown');
    const timer = setInterval(() => {
        countdown--;
        if (countdownEl) countdownEl.textContent = countdown;
        if (countdown <= 0) {
            clearInterval(timer);
            document.getElementById('resultado').style.display = 'none';
        }
    }, 1000);
    @endif

    @if(session('bloqueado'))
    let bloqueoRestantes = {{ session('restantes') }};
    const bloqueoEl = document.getElementById('bloqueo-countdown');

    function formatBloqueo(s) {
        const m = Math.floor(s / 60);
        const sec = s % 60;
        return m + ':' + String(sec).padStart(2, '0');
    }

    const bloqueoTimer = setInterval(() => {
        bloqueoRestantes--;
        if (bloqueoEl) bloqueoEl.textContent = formatBloqueo(bloqueoRestantes);
        if (bloqueoRestantes <= 0) {
            clearInterval(bloqueoTimer);
            document.getElementById('bloqueo-screen').style.display = 'none';
        }
    }, 1000);
    @endif

    @if(session('error'))
    let countdownErr = 3;
    const countdownErrEl = document.getElementById('countdown-error');
    const timerErr = setInterval(() => {
        countdownErr--;
        if (countdownErrEl) countdownErrEl.textContent = countdownErr;
        if (countdownErr <= 0) {
            clearInterval(timerErr);
            document.getElementById('error-screen').style.display = 'none';
        }
    }, 1000);
    @endif
</script>
@endpush
