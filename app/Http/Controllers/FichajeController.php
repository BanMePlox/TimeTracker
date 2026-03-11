<?php

namespace App\Http\Controllers;

use App\Models\Fichaje;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FichajeController extends Controller
{
    private const MAX_INTENTOS  = 5;
    private const BLOQUEO_1     = 60;       // segundos (1er bloqueo)
    private const BLOQUEO_2     = 300;      // segundos (2º bloqueo)

    public function index()
    {
        return view('fichaje.index');
    }

    public function store(Request $request)
    {
        $request->validate(['pin' => 'required|string|size:4']);

        $ip          = $request->ip();
        $keyLock     = "pin_lock_{$ip}";
        $keyIntentos = "pin_intentos_{$ip}";
        $keyNivel    = "pin_nivel_{$ip}";   // 0 = ninguno, 1 = primer bloqueo ya usado

        // ── Comprobar si está bloqueado ────────────────────────────────────────
        $bloqueadoHasta = Cache::get($keyLock);
        if ($bloqueadoHasta) {
            $restantes = $bloqueadoHasta - now()->timestamp;
            if ($restantes > 0) {
                return back()->with([
                    'bloqueado'  => true,
                    'restantes'  => $restantes,
                ]);
            }
            // El tiempo de bloqueo ya expiró, limpiar la clave
            Cache::forget($keyLock);
        }

        // ── Validar PIN ────────────────────────────────────────────────────────
        $user = User::where('pin', $request->pin)->first();

        if (!$user) {
            $intentos = Cache::get($keyIntentos, 0) + 1;

            if ($intentos >= self::MAX_INTENTOS) {
                $nivel    = Cache::get($keyNivel, 0);
                $duracion = ($nivel === 0) ? self::BLOQUEO_1 : self::BLOQUEO_2;
                $nuevoNivel = ($nivel === 0) ? 1 : 0;   // alterna: 2º bloqueo resetea el ciclo

                Cache::put($keyLock, now()->timestamp + $duracion, $duracion + 10);
                Cache::put($keyNivel, $nuevoNivel, 3600);
                Cache::forget($keyIntentos);

                return back()->with([
                    'bloqueado'  => true,
                    'restantes'  => $duracion,
                ]);
            }

            Cache::put($keyIntentos, $intentos, 600);   // expira en 10 min sin actividad

            $restantes = self::MAX_INTENTOS - $intentos;
            return back()->with('error', "PIN incorrecto. {$restantes} " . ($restantes === 1 ? 'intento restante.' : 'intentos restantes.'));
        }

        // ── PIN correcto: limpiar contadores ──────────────────────────────────
        Cache::forget($keyIntentos);
        Cache::forget($keyNivel);

        $ultimoFichaje = Fichaje::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $tipo = (!$ultimoFichaje || $ultimoFichaje->tipo === 'salida') ? 'entrada' : 'salida';

        Fichaje::create(['user_id' => $user->id, 'tipo' => $tipo]);

        $mensaje = $tipo === 'entrada'
            ? "¡Bienvenido, {$user->name}! Entrada registrada."
            : "¡Hasta pronto, {$user->name}! Salida registrada.";

        return back()->with([
            'success' => $mensaje,
            'tipo'    => $tipo,
            'nombre'  => $user->name,
        ]);
    }
}
