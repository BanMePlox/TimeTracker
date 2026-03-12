<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Fichaje;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Today's status
        $ultimoFichaje = $user->fichajes()->whereDate('created_at', today())->latest()->first();
        $dentroAhora   = $ultimoFichaje && in_array($ultimoFichaje->tipo, ['entrada', 'reanudacion']);

        // Hours this week
        $inicioSemana = Carbon::now()->startOfWeek();
        $fichajesSemana = $user->fichajes()->where('created_at', '>=', $inicioSemana)->orderBy('created_at')->get();
        $minutosSemanales = $this->calcularMinutos($fichajesSemana);
        $horasEstaSemana  = round($minutosSemanales / 60, 2);

        // Hours this month
        $inicioMes = Carbon::now()->startOfMonth();
        $fichajesMes = $user->fichajes()->where('created_at', '>=', $inicioMes)->orderBy('created_at')->get();
        $minutosMensuales = $this->calcularMinutos($fichajesMes);
        $horasEsteMes     = round($minutosMensuales / 60, 2);

        // Expected hours
        $diasSemana     = max(now()->dayOfWeek ?: 7, 1);
        $diasMes        = now()->day;
        $esperadoSemana = round($user->horas_diarias * $diasSemana, 2);
        $esperadoMes    = round($user->horas_diarias * $diasMes, 2);

        // Last 7 fichajes
        $ultimosFichajes = $user->fichajes()->latest()->take(7)->get();

        // Pending absences
        $ausenciasPendientes = $user->ausencias()->where('estado', 'pendiente')->count();

        return view('empleado.dashboard', compact(
            'user', 'dentroAhora', 'ultimoFichaje',
            'horasEstaSemana', 'horasEsteMes',
            'esperadoSemana', 'esperadoMes',
            'ultimosFichajes', 'ausenciasPendientes'
        ));
    }

    private function calcularMinutos($fichajes): int
    {
        $minutos = 0;
        $trabajandoDesde = null;
        foreach ($fichajes as $f) {
            if (in_array($f->tipo, ['entrada', 'reanudacion'])) {
                $trabajandoDesde = $f->created_at;
            } elseif (in_array($f->tipo, ['pausa', 'salida']) && $trabajandoDesde) {
                $minutos += $trabajandoDesde->diffInMinutes($f->created_at);
                $trabajandoDesde = null;
            }
        }
        return $minutos;
    }
}
