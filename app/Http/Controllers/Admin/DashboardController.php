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

        // Employees currently "in" (last fichaje today is entrada)
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
                return $ultimo && $ultimo->tipo === 'entrada';
            })
            ->count();

        // Total hours worked today by all employees
        $fichajesDeHoy = Fichaje::whereDate('created_at', today())
            ->orderBy('user_id')
            ->orderBy('created_at')
            ->get()
            ->groupBy('user_id');

        $minutosHoy = 0;
        foreach ($fichajesDeHoy as $userFichajes) {
            $entradaPendiente = null;
            foreach ($userFichajes->sortBy('created_at') as $fichaje) {
                if ($fichaje->tipo === 'entrada') {
                    $entradaPendiente = $fichaje;
                } elseif ($fichaje->tipo === 'salida' && $entradaPendiente) {
                    $minutosHoy += $entradaPendiente->created_at->diffInMinutes($fichaje->created_at);
                    $entradaPendiente = null;
                }
            }
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
                $ep = null;
                foreach ($uf->sortBy('created_at') as $f) {
                    if ($f->tipo === 'entrada') { $ep = $f; }
                    elseif ($f->tipo === 'salida' && $ep) {
                        $minDia += $ep->created_at->diffInMinutes($f->created_at);
                        $ep = null;
                    }
                }
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
            $ep = null; $min = 0;
            foreach ($fichajes as $f) {
                if ($f->tipo === 'entrada') { $ep = $f; }
                elseif ($f->tipo === 'salida' && $ep) {
                    $min += $ep->created_at->diffInMinutes($f->created_at);
                    $ep = null;
                }
            }
            $empleadoLabels[] = explode(' ', $emp->name)[0];
            $empleadoHoras[] = round($min / 60, 1);
            $empleadoEsperado[] = round($emp->horas_diarias * max(now()->dayOfWeek ?: 7, 1), 1);
        }

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
            'empleadoEsperado'
        ));
    }
}
