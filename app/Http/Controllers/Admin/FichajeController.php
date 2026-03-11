<?php

namespace App\Http\Controllers\Admin;

use App\Exports\FichajesExport;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Fichaje;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FichajeController extends Controller
{
    public function index(Request $request)
    {
        $query = Fichaje::with('user')->latest();

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $fichajes = $query->paginate(20)->withQueryString();
        $usuarios = User::orderBy('name')->get();

        return view('admin.fichajes.index', compact('fichajes', 'usuarios'));
    }

    public function export(Request $request)
    {
        $filters = $request->only(['user_id', 'fecha_desde', 'fecha_hasta']);
        return Excel::download(new FichajesExport($filters), 'fichajes_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function edit(Fichaje $fichaje)
    {
        $fichaje->load('user');
        return view('admin.fichajes.edit', compact('fichaje'));
    }

    public function update(Request $request, Fichaje $fichaje)
    {
        $request->validate([
            'tipo' => 'required|in:entrada,salida',
            'fecha' => 'required|date',
            'hora' => 'required',
        ]);

        $datetime = Carbon::parse($request->fecha . ' ' . $request->hora);

        $tipoAnterior = $fichaje->tipo;
        $fechaAnterior = $fichaje->created_at->format('d/m/Y H:i');

        $fichaje->update([
            'tipo' => $request->tipo,
            'created_at' => $datetime,
            'updated_at' => $datetime,
        ]);

        ActivityLog::registrar(
            'fichaje_corregido',
            "Fichaje #{$fichaje->id} de {$fichaje->user->name} corregido: {$tipoAnterior} {$fechaAnterior} → {$request->tipo} {$datetime->format('d/m/Y H:i')}",
            'Fichaje',
            $fichaje->id
        );
        return redirect()->route('admin.fichajes.index')
            ->with('success', 'Fichaje corregido correctamente.');
    }

    public function destroy(Fichaje $fichaje)
    {
        ActivityLog::registrar(
            'fichaje_eliminado',
            "Fichaje #{$fichaje->id} de {$fichaje->user->name} eliminado ({$fichaje->tipo}, {$fichaje->created_at->format('d/m/Y H:i')})",
            'Fichaje',
            $fichaje->id
        );
        $fichaje->delete();
        return back()->with('success', 'Fichaje eliminado correctamente.');
    }
}
