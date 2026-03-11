<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class PresenciaController extends Controller
{
    public function index()
    {
        $empleados = User::where('role', 'empleado')->get();

        $presentes = $empleados->filter(function ($user) {
            $ultimo = $user->fichajes()
                ->whereDate('created_at', today())
                ->latest()
                ->first();
            if ($ultimo && $ultimo->tipo === 'entrada') {
                $user->entrada_at = $ultimo->created_at;
                return true;
            }
            return false;
        })->values();

        $ausentes = $empleados->filter(function ($user) {
            $ultimo = $user->fichajes()
                ->whereDate('created_at', today())
                ->latest()
                ->first();
            return !$ultimo || $ultimo->tipo === 'salida';
        })->map(function ($user) {
            $ultimo = $user->fichajes()
                ->whereDate('created_at', today())
                ->latest()
                ->first();
            $user->salida_at = $ultimo?->created_at;
            return $user;
        })->values();

        return view('admin.presencia.index', compact('presentes', 'ausentes'));
    }
}
