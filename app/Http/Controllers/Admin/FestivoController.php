<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Festivo;
use Illuminate\Http\Request;

class FestivoController extends Controller
{
    public function index()
    {
        $festivos = Festivo::orderBy('fecha')->paginate(20);
        return view('admin.festivos.index', compact('festivos'));
    }

    public function create()
    {
        return view('admin.festivos.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha'       => 'required|date|unique:festivos,fecha',
            'nombre'      => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:255',
        ]);

        Festivo::create($request->only('fecha', 'nombre', 'descripcion'));

        return redirect()->route('admin.festivos.index')->with('success', 'Festivo añadido correctamente.');
    }

    public function destroy(Festivo $festivo)
    {
        $festivo->delete();
        return redirect()->route('admin.festivos.index')->with('success', 'Festivo eliminado.');
    }
}
