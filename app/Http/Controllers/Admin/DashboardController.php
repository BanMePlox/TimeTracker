<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fichaje;
use App\Models\User;
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

        return view('admin.dashboard', compact(
            'totalUsuarios',
            'fichajesHoy',
            'ultimosFichajes',
            'empleadosPresentes',
            'horasHoy'
        ));
    }
}
