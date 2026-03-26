<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fichaje;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsuarios = User::where('role', 'empleado')->count();
        $fichajesHoy = Fichaje::whereDate('created_at', today())->count();
        $ultimosFichajes = Fichaje::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Employees currently "in" (last fichaje today is entrada or reanudacion)
        $empleadosPresentes = User::where('role', 'empleado')
            ->whereHas('fichajes', function ($query) {
                $query->whereDate('created_at', today());
            })
            ->get()
            ->filter(function ($user) {
                $ultimo = $user->fichajes()
                    ->whereDate('created_at', today())
                    ->latest()
                    ->first();
                return $ultimo && in_array($ultimo->tipo, ['entrada', 'reanudacion']);
            })
            ->count();

        // Total hours worked today by all employees (respects pausa/reanudacion)
        $fichajesDeHoy = Fichaje::whereDate('created_at', today())
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        $minutosHoy = 0;
        foreach ($fichajesDeHoy as $userFichajes) {
            $minutosHoy += $this->calcularMinutos($userFichajes->sortBy('created_at'));
        }
        $horasHoy = round($minutosHoy / 60, 1);

        // Chart: hours per day for last 7 days (all employees total)
        $chartLabels = [];
        $chartHoras = [];
        for ($i = 6; $i >= 0; $i--) {
            $dia = Carbon::now()->subDays($i);
            $chartLabels[] = $dia->format('d/m');
            $fichajesDia = Fichaje::whereDate('created_at', $dia->toDateString())
                ->orderBy('user_id')->orderBy('created_at')->get()->groupBy('user_id');
            $minDia = 0;
            foreach ($fichajesDia as $uf) {
                $minDia += $this->calcularMinutos($uf->sortBy('created_at'));
            }
            $chartHoras[] = round($minDia / 60, 1);
        }

        // Chart: hours per employee this week
        $inicioSemana = Carbon::now()->startOfWeek();
        $empleados = User::where('role', 'empleado')->orderBy('name')->get();
        $empleadoLabels = [];
        $empleadoHoras = [];
        $empleadoEsperado = [];
        foreach ($empleados as $emp) {
            $fichajes = $emp->fichajes()->where('created_at', '>=', $inicioSemana)->orderBy('created_at')->get();
            $min = $this->calcularMinutos($fichajes);
            $empleadoLabels[] = explode(' ', $emp->name)[0];
            $empleadoHoras[] = round($min / 60, 1);
            $empleadoEsperado[] = round($emp->horas_diarias * max(now()->dayOfWeek ?: 7, 1), 1);
        }

        // Employees who have exceeded their daily hours without clocking out (30 min grace period)
        $sinSalida = User::where('role', 'empleado')
            ->get()
            ->filter(function ($user) {
                $entrada = $user->fichajes()
                    ->whereDate('created_at', today())
                    ->where('tipo', 'entrada')
                    ->oldest()
                    ->first();
                if (!$entrada) return false;

                $ultimo = $user->fichajes()
                    ->whereDate('created_at', today())
                    ->latest()
                    ->first();
                if (!$ultimo || !in_array($ultimo->tipo, ['entrada', 'reanudacion'])) {
                    return false;
                }

                $minutosEsperados = ($user->horas_diarias ?? 8) * 60;
                $minutosDesdeEntrada = $entrada->created_at->diffInMinutes(now());
                return $minutosDesdeEntrada >= ($minutosEsperados + 30);
            })
            ->map(function ($user) {
                $entrada = $user->fichajes()
                    ->whereDate('created_at', today())
                    ->where('tipo', 'entrada')
                    ->oldest()
                    ->first();
                $minutosDesdeEntrada = $entrada->created_at->diffInMinutes(now());
                $minutosEsperados = ($user->horas_diarias ?? 8) * 60;
                return [
                    'name'    => $user->name,
                    'desde'   => $entrada->created_at->format('H:i'),
                    'exceso'  => $minutosDesdeEntrada - $minutosEsperados,
                ];
            })
            ->values();

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'fichajesHoy',
            'ultimosFichajes',
            'empleadosPresentes',
            'horasHoy',
            'chartLabels',
            'chartHoras',
            'empleadoLabels',
            'empleadoHoras',
            'empleadoEsperado',
            'sinSalida'
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
