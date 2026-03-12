<?php

namespace App\Http\Controllers\Empleado;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FichajeController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $desde = $request->filled('fecha_desde')
            ? Carbon::parse($request->fecha_desde)->startOfDay()
            : Carbon::now()->subDays(29)->startOfDay();
        $hasta = $request->filled('fecha_hasta')
            ? Carbon::parse($request->fecha_hasta)->endOfDay()
            : Carbon::now()->endOfDay();

        $fichajes = $user->fichajes()
            ->whereBetween('created_at', [$desde, $hasta])
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('empleado.fichajes.index', compact('fichajes', 'desde', 'hasta'));
    }
}
