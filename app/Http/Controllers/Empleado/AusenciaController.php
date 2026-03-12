<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use App\Models\Ausencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AusenciaController extends Controller
{
    public function index()
    {
        $ausencias = Auth::user()->ausencias()->latest('fecha_inicio')->paginate(20);
        return view('empleado.ausencias.index', compact('ausencias'));
    }

    public function create()
    {
        return view('empleado.ausencias.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'tipo'        => 'required|in:vacaciones,baja_medica,ausencia_justificada,ausencia_injustificada',
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin'    => 'required|date|after_or_equal:fecha_inicio',
            'descripcion'  => 'nullable|string|max:500',
        ]);

        $overlap = $user->ausencias()
            ->where('fecha_fin', '>=', $request->fecha_inicio)
            ->where('fecha_inicio', '<=', $request->fecha_fin)
            ->exists();

        if ($overlap) {
            return back()->withInput()->withErrors([
                'fecha_inicio' => 'Ya tienes una ausencia registrada en ese rango de fechas.',
            ]);
        }

        Ausencia::create([
            'user_id'      => $user->id,
            'tipo'         => $request->tipo,
            'fecha_inicio' => $request->fecha_inicio,
            'fecha_fin'    => $request->fecha_fin,
            'descripcion'  => $request->descripcion,
            'estado'       => 'pendiente',
        ]);

        return redirect()->route('empleado.ausencias.index')
            ->with('success', 'Solicitud de ausencia enviada. Pendiente de aprobación.');
    }
}
