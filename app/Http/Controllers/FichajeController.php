<?php

namespace App\Http\Controllers;

use App\Models\Fichaje;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FichajeController extends Controller
{
    private const MAX_INTENTOS  = 5;
    private const BLOQUEO_1     = 60;
    private const BLOQUEO_2     = 300;

    // Tipos que indican que el empleado está trabajando (no en pausa)
    private const TIPOS_DENTRO  = ['entrada', 'reanudacion'];

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
        $keyNivel    = "pin_nivel_{$ip}";

        // ── Comprobar bloqueo ──────────────────────────────────────────────────
        $bloqueadoHasta = Cache::get($keyLock);
        if ($bloqueadoHasta) {
            $restantes = $bloqueadoHasta - now()->timestamp;
            if ($restantes > 0) {
                return back()->with(['bloqueado' => true, 'restantes' => $restantes]);
            }
            Cache::forget($keyLock);
        }

        // ── Validar PIN ────────────────────────────────────────────────────────
        $user = User::where('pin', $request->pin)->first();

        if (!$user) {
            $intentos = Cache::get($keyIntentos, 0) + 1;

            if ($intentos >= self::MAX_INTENTOS) {
                $nivel    = Cache::get($keyNivel, 0);
                $duracion = ($nivel === 0) ? self::BLOQUEO_1 : self::BLOQUEO_2;
                Cache::put($keyLock, now()->timestamp + $duracion, $duracion + 10);
                Cache::put($keyNivel, ($nivel === 0) ? 1 : 0, 3600);
                Cache::forget($keyIntentos);
                return back()->with(['bloqueado' => true, 'restantes' => $duracion]);
            }

            Cache::put($keyIntentos, $intentos, 600);
            $restantes = self::MAX_INTENTOS - $intentos;
            return back()->with('error', "PIN incorrecto. {$restantes} " . ($restantes === 1 ? 'intento restante.' : 'intentos restantes.'));
        }

        // ── PIN correcto: limpiar contadores ──────────────────────────────────
        Cache::forget($keyIntentos);
        Cache::forget($keyNivel);

        // ── Comprobar que el usuario está activo ───────────────────────────────
        if (!$user->active) {
            return back()->with('error', 'Usuario desactivado. Contacta con el administrador.');
        }

        // ── Determinar estado actual ───────────────────────────────────────────
        $ultimoFichaje = Fichaje::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->latest()
            ->first();

        $estadoActual = $ultimoFichaje ? $ultimoFichaje->tipo : null;

        // ── Si se envía acción específica (segunda parte del flujo) ────────────
        $tipoSolicitado = $request->input('tipo');
        $tiposValidos   = ['entrada', 'salida', 'pausa', 'reanudacion'];

        if ($tipoSolicitado && in_array($tipoSolicitado, $tiposValidos)) {
            Fichaje::create(['user_id' => $user->id, 'tipo' => $tipoSolicitado]);
            return $this->respuestaExito($user->name, $tipoSolicitado);
        }

        // ── Auto-acción si el estado es claro (fuera → entrada) ───────────────
        if (!$estadoActual || $estadoActual === 'salida') {
            Fichaje::create(['user_id' => $user->id, 'tipo' => 'entrada']);
            return $this->respuestaExito($user->name, 'entrada');
        }

        // ── Estado requiere elección: dentro o en pausa ───────────────────────
        if (in_array($estadoActual, self::TIPOS_DENTRO)) {
            $acciones = ['pausa', 'salida'];
        } else {
            // 'pausa'
            $acciones = ['reanudacion', 'salida'];
        }

        return back()->with([
            'pin_validado' => $request->pin,
            'user_nombre'  => $user->name,
            'acciones'     => $acciones,
            'estado_actual' => $estadoActual,
        ]);
    }

    private function respuestaExito(string $nombre, string $tipo)
    {
        $mensajes = [
            'entrada'      => "¡Bienvenido, {$nombre}! Entrada registrada.",
            'salida'       => "¡Hasta pronto, {$nombre}! Salida registrada.",
            'pausa'        => "Pausa registrada, {$nombre}.",
            'reanudacion'  => "¡Reanudando, {$nombre}!",
        ];

        return back()->with([
            'success' => $mensajes[$tipo] ?? "Fichaje registrado.",
            'tipo'    => $tipo,
            'nombre'  => $nombre,
        ]);
    }
}
