<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

class PresenciaController extends Controller {
    public function index() {
        $empleados = User::where('role', 'empleado')->get();

        $presentes = $empleados->filter(function ($user) {
            $ultimo = $user->fichajes()->whereDate('created_at', today())->latest()->first();
            if ($ultimo && $ultimo->tipo === 'entrada') {
                $user->entrada_at = $ultimo->created_at;
                return true;
            }
            return false;
        })->values();

        $ausentes = $empleados->filter(function ($user) {
            $ultimo = $user->fichajes()->whereDate('created_at', today())->latest()->first();
            return !$ultimo || $ultimo->tipo === 'salida';
        })->values();

        return response()->json([
            'total_empleados' => $empleados->count(),
            'presentes' => $presentes->count(),
            'ausentes' => $ausentes->count(),
            'empleados_dentro' => $presentes->map(fn($u) => [
                'id' => $u->id,
                'nombre' => $u->name,
                'entrada_desde' => $u->entrada_at,
                'minutos_dentro' => $u->entrada_at ? now()->diffInMinutes($u->entrada_at) : null,
            ]),
            'empleados_fuera' => $ausentes->map(fn($u) => ['id' => $u->id, 'nombre' => $u->name]),
        ]);
    }
}
