<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fichaje;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InformeController extends Controller
{
    public function index(Request $request)
    {
        $usuarios = User::orderBy('name')->get();

        $query = Fichaje::with('user')->orderBy('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        } else {
            $query->whereDate('created_at', '>=', Carbon::now()->subDays(29)->toDateString());
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $fichajes = $query->get();

        // Group by user, then by date, then pair entrada/salida
        $informe = [];

        $fichajesPorUsuario = $fichajes->groupBy('user_id');
        foreach ($fichajesPorUsuario as $userId => $userFichajes) {
            $usuario = $userFichajes->first()->user;
            $fichajesPorDia = $userFichajes->groupBy(function ($f) {
                return $f->created_at->format('Y-m-d');
            });

            foreach ($fichajesPorDia as $fecha => $fichajesDia) {
                $sesiones = $this->calcularHoras($fichajesDia);
                foreach ($sesiones as $sesion) {
                    $informe[] = [
                        'usuario' => $usuario,
                        'fecha' => $fecha,
                        'sesion' => $sesion,
                    ];
                }
            }
        }

        // Sort by date desc, then by user name
        usort($informe, function ($a, $b) {
            $dateComp = strcmp($b['fecha'], $a['fecha']);
            if ($dateComp !== 0) return $dateComp;
            return strcmp($a['usuario']->name, $b['usuario']->name);
        });

        return view('admin.informes.index', compact('informe', 'usuarios'));
    }

    public function export(Request $request)
    {
        $query = Fichaje::with('user')->orderBy('created_at');

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        } else {
            $query->whereDate('created_at', '>=', Carbon::now()->subDays(29)->toDateString());
        }
        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $fichajes = $query->get();

        $fichajesPorUsuario = $fichajes->groupBy('user_id');
        $exportData = [];

        foreach ($fichajesPorUsuario as $userId => $userFichajes) {
            $usuario = $userFichajes->first()->user;
            $fichajesPorDia = $userFichajes->groupBy(function ($f) {
                return $f->created_at->format('Y-m-d');
            });

            foreach ($fichajesPorDia as $fecha => $fichajesDia) {
                $sesiones = $this->calcularHoras($fichajesDia);
                foreach ($sesiones as $sesion) {
                    $exportData[] = [
                        $usuario->name,
                        Carbon::parse($fecha)->format('d/m/Y'),
                        $sesion['entrada']->created_at->format('H:i:s'),
                        $sesion['salida'] ? $sesion['salida']->created_at->format('H:i:s') : 'En curso',
                        $sesion['horas'] !== null ? $sesion['horas'] . 'h' : '—',
                    ];
                }
            }
        }

        $filename = 'informe_horas_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($exportData) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM
            fputcsv($file, ['Empleado', 'Fecha', 'Entrada', 'Salida', 'Horas trabajadas'], ';');
            foreach ($exportData as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calcularHoras($fichajes)
    {
        $sesiones = [];
        $entradaPendiente = null;

        foreach ($fichajes->sortBy('created_at') as $fichaje) {
            if ($fichaje->tipo === 'entrada') {
                $entradaPendiente = $fichaje;
            } elseif ($fichaje->tipo === 'salida' && $entradaPendiente) {
                $minutos = $entradaPendiente->created_at->diffInMinutes($fichaje->created_at);
                $sesiones[] = [
                    'entrada' => $entradaPendiente,
                    'salida' => $fichaje,
                    'minutos' => $minutos,
                    'horas' => round($minutos / 60, 2),
                ];
                $entradaPendiente = null;
            }
        }

        if ($entradaPendiente) {
            $sesiones[] = [
                'entrada' => $entradaPendiente,
                'salida' => null,
                'minutos' => null,
                'horas' => null,
            ];
        }

        return $sesiones;
    }
}
