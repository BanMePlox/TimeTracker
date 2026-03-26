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

    public function resumen(Request $request)
    {
        $mes    = (int) ($request->mes  ?? now()->month);
        $anio   = (int) ($request->anio ?? now()->year);

        $desde = Carbon::create($anio, $mes, 1)->startOfDay();
        $hasta = $desde->copy()->endOfMonth();

        // Working days in the month (Mon–Fri, excluding today if in the future)
        $diasLaborables = 0;
        $cursor = $desde->copy();
        $limite = $hasta->copy()->min(now());
        while ($cursor->lte($limite)) {
            if ($cursor->isWeekday()) $diasLaborables++;
            $cursor->addDay();
        }

        $empleados = User::where('role', 'empleado')->orderBy('name')->get();
        $resumen = [];

        foreach ($empleados as $emp) {
            $fichajes = $emp->fichajes()
                ->whereBetween('created_at', [$desde, $hasta])
                ->orderBy('created_at')
                ->get();

            $fichajesPorDia = $fichajes->groupBy(fn($f) => $f->created_at->format('Y-m-d'));

            $totalMinutos = 0;
            $diasTrabajados = 0;
            foreach ($fichajesPorDia as $fDia) {
                $min = $this->calcularMinutosSimple($fDia->sortBy('created_at'));
                if ($min > 0) {
                    $totalMinutos += $min;
                    $diasTrabajados++;
                }
            }

            $horasEsperadas = $diasLaborables * ($emp->horas_diarias ?? 8);
            $horasTrabajadas = round($totalMinutos / 60, 1);
            $diferencia = round($horasTrabajadas - $horasEsperadas, 1);

            $resumen[] = [
                'usuario'          => $emp,
                'dias_trabajados'  => $diasTrabajados,
                'dias_laborables'  => $diasLaborables,
                'horas_trabajadas' => $horasTrabajadas,
                'horas_esperadas'  => $horasEsperadas,
                'diferencia'       => $diferencia,
            ];
        }

        $meses = collect(range(1, 12))->mapWithKeys(fn($m) => [
            $m => Carbon::create(null, $m)->locale('es')->monthName
        ]);

        $mesNombre = Carbon::create($anio, $mes, 1)->locale('es')->monthName;

        return view('admin.informes.resumen', compact('resumen', 'mes', 'anio', 'meses', 'mesNombre'));
    }

    public function pdf(Request $request)
    {
        $usuarios = User::where('role', 'empleado')->orderBy('name')->get();

        $mes    = (int) ($request->mes  ?? now()->month);
        $anio   = (int) ($request->anio ?? now()->year);
        $userId = $request->user_id;

        $desde = Carbon::create($anio, $mes, 1)->startOfDay();
        $hasta = $desde->copy()->endOfMonth();

        $query = Fichaje::with('user')->orderBy('created_at');
        if ($userId) {
            $query->where('user_id', $userId);
        }
        $query->whereBetween('created_at', [$desde, $hasta]);
        $fichajes = $query->get();

        $fichajesPorUsuario = $fichajes->groupBy('user_id');
        $resumen = [];

        foreach ($fichajesPorUsuario as $uid => $userFichajes) {
            $usuario = $userFichajes->first()->user;
            $fichajesPorDia = $userFichajes->groupBy(fn($f) => $f->created_at->format('Y-m-d'));
            $dias = [];
            $totalMinutos = 0;
            foreach ($fichajesPorDia as $fecha => $fDia) {
                $sesiones = $this->calcularHoras($fDia);
                $minDia = (int) collect($sesiones)->sum('minutos');
                $totalMinutos += $minDia;
                $dias[$fecha] = ['sesiones' => $sesiones, 'minutos' => $minDia];
            }
            ksort($dias);
            $resumen[] = [
                'usuario'       => $usuario,
                'dias'          => $dias,
                'total_minutos' => $totalMinutos,
                'total_horas'   => round($totalMinutos / 60, 2),
            ];
        }

        $mesNombre = Carbon::create($anio, $mes, 1)->locale('es')->monthName;

        return view('admin.informes.pdf', compact('resumen', 'usuarios', 'mes', 'anio', 'mesNombre', 'userId'));
    }

    private function calcularMinutosSimple($fichajes): int
    {
        $minutos = 0;
        $desde = null;
        foreach ($fichajes as $f) {
            if (in_array($f->tipo, ['entrada', 'reanudacion'])) {
                $desde = $f->created_at;
            } elseif (in_array($f->tipo, ['pausa', 'salida']) && $desde) {
                $minutos += $desde->diffInMinutes($f->created_at);
                $desde = null;
            }
        }
        // Still clocked in
        if ($desde) {
            $minutos += $desde->diffInMinutes(now());
        }
        return $minutos;
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
