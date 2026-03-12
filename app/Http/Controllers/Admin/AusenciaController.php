<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Ausencia;
use App\Models\Festivo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AusenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Ausencia::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        if ($request->filled('fecha_desde')) {
            $query->where('fecha_fin', '>=', $request->fecha_desde);
        }
        if ($request->filled('fecha_hasta')) {
            $query->where('fecha_inicio', '<=', $request->fecha_hasta);
        }

        $ausencias = $query->paginate(20)->withQueryString();
        $usuarios = User::orderBy('name')->get();

        return view('admin.ausencias.index', compact('ausencias', 'usuarios'));
    }

    public function create()
    {
        $usuarios = User::where('role', 'empleado')->orderBy('name')->get();
        return view('admin.ausencias.create', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|in:vacaciones,baja_medica,ausencia_justificada,ausencia_injustificada',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
        ]);

        // Check for overlapping absences for the same user
        $overlap = Ausencia::where('user_id', $request->user_id)
            ->where('fecha_inicio', '<=', $request->fecha_fin)
            ->where('fecha_fin', '>=', $request->fecha_inicio)
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'fecha_inicio' => 'Ya existe una ausencia registrada para este empleado en el rango de fechas indicado.',
            ]);
        }

        $ausencia = Ausencia::create($request->only([
            'user_id', 'tipo', 'fecha_inicio', 'fecha_fin', 'descripcion', 'estado',
        ]));

        $empleado = \App\Models\User::find($request->user_id);
        ActivityLog::registrar('ausencia_creada', "Ausencia registrada para {$empleado->name}: {$ausencia->tipo_label} ({$request->fecha_inicio} - {$request->fecha_fin})", 'Ausencia', $ausencia->id);
        return redirect()->route('admin.ausencias.index')
            ->with('success', 'Ausencia registrada correctamente.');
    }

    public function show(Ausencia $ausencia)
    {
        $ausencia->load('user');
        return view('admin.ausencias.show', compact('ausencia'));
    }

    public function edit(Ausencia $ausencia)
    {
        $ausencia->load('user');
        $usuarios = User::where('role', 'empleado')->orderBy('name')->get();
        return view('admin.ausencias.edit', compact('ausencia', 'usuarios'));
    }

    public function update(Request $request, Ausencia $ausencia)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tipo' => 'required|in:vacaciones,baja_medica,ausencia_justificada,ausencia_injustificada',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
            'descripcion' => 'nullable|string|max:1000',
            'estado' => 'required|in:pendiente,aprobada,rechazada',
        ]);

        // Check for overlapping absences (excluding current)
        $overlap = Ausencia::where('user_id', $request->user_id)
            ->where('id', '!=', $ausencia->id)
            ->where('fecha_inicio', '<=', $request->fecha_fin)
            ->where('fecha_fin', '>=', $request->fecha_inicio)
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'fecha_inicio' => 'Ya existe una ausencia registrada para este empleado en el rango de fechas indicado.',
            ]);
        }

        $ausencia->update($request->only([
            'user_id', 'tipo', 'fecha_inicio', 'fecha_fin', 'descripcion', 'estado',
        ]));

        ActivityLog::registrar('ausencia_actualizada', "Ausencia #{$ausencia->id} de {$ausencia->user->name} actualizada", 'Ausencia', $ausencia->id);
        return redirect()->route('admin.ausencias.index')
            ->with('success', 'Ausencia actualizada correctamente.');
    }

    public function destroy(Ausencia $ausencia)
    {
        ActivityLog::registrar('ausencia_eliminada', "Ausencia #{$ausencia->id} de {$ausencia->user->name} eliminada ({$ausencia->tipo_label})", 'Ausencia', $ausencia->id);
        $ausencia->delete();
        return back()->with('success', 'Ausencia eliminada correctamente.');
    }

    public function aprobar(Ausencia $ausencia)
    {
        $ausencia->update(['estado' => 'aprobada']);
        ActivityLog::registrar('ausencia_aprobada', "Ausencia #{$ausencia->id} de {$ausencia->user->name} aprobada", 'Ausencia', $ausencia->id);
        return back()->with('success', 'Ausencia aprobada correctamente.');
    }

    public function rechazar(Ausencia $ausencia)
    {
        $ausencia->update(['estado' => 'rechazada']);
        ActivityLog::registrar('ausencia_rechazada', "Ausencia #{$ausencia->id} de {$ausencia->user->name} rechazada", 'Ausencia', $ausencia->id);
        return back()->with('success', 'Ausencia rechazada correctamente.');
    }

    public function calendario(Request $request)
    {
        $mes  = $request->integer('mes', now()->month);
        $anio = $request->integer('anio', now()->year);

        // Clamp values
        $mes  = max(1, min(12, $mes));
        $anio = max(2020, min(2030, $anio));

        $inicio = Carbon::createFromDate($anio, $mes, 1)->startOfMonth();
        $fin    = $inicio->copy()->endOfMonth();

        // All approved absences that overlap this month
        $ausencias = Ausencia::with('user')
            ->where('estado', 'aprobada')
            ->where('fecha_inicio', '<=', $fin)
            ->where('fecha_fin', '>=', $inicio)
            ->get();

        // Festivos this month
        $festivosMes = Festivo::whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->get()
            ->keyBy(fn($f) => $f->fecha->format('Y-m-d'));

        // Build day-by-day map
        $dias = [];
        for ($day = $inicio->copy(); $day->lte($fin); $day->addDay()) {
            $fecha = $day->format('Y-m-d');
            $dias[$fecha] = [
                'numero'    => $day->day,
                'diaSemana' => $day->dayOfWeek, // 0=Dom, 6=Sab
                'ausencias' => $ausencias->filter(
                    fn($a) => $a->fecha_inicio->lte($day) && $a->fecha_fin->gte($day)
                )->values(),
                'festivo'   => $festivosMes->get($fecha),
            ];
        }

        // Navigation
        $prevMes  = $inicio->copy()->subMonth();
        $nextMes  = $inicio->copy()->addMonth();

        return view('admin.ausencias.calendario', compact(
            'dias', 'mes', 'anio', 'inicio', 'prevMes', 'nextMes'
        ));
    }
}
