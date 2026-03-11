<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\FichajeResource;
use App\Models\Fichaje;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FichajePublicoController extends Controller {
    private const MAX_INTENTOS = 5;
    private const BLOQUEO_1 = 60;
    private const BLOQUEO_2 = 300;

    public function store(Request $request) {
        $request->validate(['pin' => 'required|string|size:4']);

        $ip = $request->ip();
        $keyLock = "pin_lock_{$ip}";
        $keyIntentos = "pin_intentos_{$ip}";
        $keyNivel = "pin_nivel_{$ip}";

        $bloqueadoHasta = Cache::get($keyLock);
        if ($bloqueadoHasta) {
            $restantes = $bloqueadoHasta - now()->timestamp;
            if ($restantes > 0) {
                return response()->json(['mensaje' => 'Terminal bloqueado por exceso de intentos.', 'bloqueado_segundos' => $restantes], 429);
            }
            Cache::forget($keyLock);
        }

        $user = User::where('pin', $request->pin)->first();
        if (!$user) {
            $intentos = Cache::get($keyIntentos, 0) + 1;
            if ($intentos >= self::MAX_INTENTOS) {
                $nivel = Cache::get($keyNivel, 0);
                $duracion = ($nivel === 0) ? self::BLOQUEO_1 : self::BLOQUEO_2;
                Cache::put($keyLock, now()->timestamp + $duracion, $duracion + 10);
                Cache::put($keyNivel, ($nivel === 0) ? 1 : 0, 3600);
                Cache::forget($keyIntentos);
                return response()->json(['mensaje' => 'Terminal bloqueado.', 'bloqueado_segundos' => $duracion], 429);
            }
            Cache::put($keyIntentos, $intentos, 600);
            return response()->json(['mensaje' => 'PIN incorrecto.', 'intentos_restantes' => self::MAX_INTENTOS - $intentos], 401);
        }

        Cache::forget($keyIntentos);
        Cache::forget($keyNivel);

        $ultimo = Fichaje::where('user_id', $user->id)->whereDate('created_at', today())->latest()->first();
        $tipo = (!$ultimo || $ultimo->tipo === 'salida') ? 'entrada' : 'salida';
        $fichaje = Fichaje::create(['user_id' => $user->id, 'tipo' => $tipo]);

        return response()->json([
            'tipo' => $tipo,
            'usuario' => $user->name,
            'fichaje' => new FichajeResource($fichaje->load('user')),
        ], 201);
    }
}
