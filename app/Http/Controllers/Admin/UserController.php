<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Fichaje;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected function generarPin(): string
    {
        do {
            $pin = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (User::where('pin', $pin)->exists());

        return $pin;
    }

    public function index()
    {
        $users = User::withCount('fichajes')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $pin = $this->generarPin();
        return view('admin.users.create', compact('pin'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,empleado',
            'pin' => 'required|size:4|unique:users',
            'horas_diarias' => 'required|numeric|min:1|max:24',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'pin' => $request->pin,
            'horas_diarias' => $request->horas_diarias,
        ]);

        ActivityLog::registrar('usuario_creado', "Empleado creado: {$request->name} ({$request->email})", 'User');
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario creado correctamente.');
    }

    public function show(User $user)
    {
        $fichajes = $user->fichajes()->latest()->paginate(20);

        // Calculate hours per day for the last 30 days
        $desde = Carbon::now()->subDays(29)->startOfDay();
        $fichajesRecientes = $user->fichajes()
            ->where('created_at', '>=', $desde)
            ->orderBy('created_at')
            ->get();

        $horasPorDia = [];
        $fichajesPorDia = $fichajesRecientes->groupBy(function ($f) {
            return $f->created_at->format('Y-m-d');
        });

        foreach ($fichajesPorDia as $fecha => $fichajesDia) {
            $entradaPendiente = null;
            $sesiones = [];
            foreach ($fichajesDia->sortBy('created_at') as $fichaje) {
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
            $totalMinutos = collect($sesiones)->sum('minutos');
            $horasPorDia[$fecha] = [
                'sesiones' => $sesiones,
                'total_minutos' => $totalMinutos,
                'total_horas' => round($totalMinutos / 60, 2),
            ];
        }
        krsort($horasPorDia);

        // Totals
        $inicioSemana = Carbon::now()->startOfWeek();
        $inicioMes = Carbon::now()->startOfMonth();
        $minutosEstaSemana = 0;
        $minutosEsteMes = 0;
        foreach ($horasPorDia as $fecha => $dia) {
            $fechaCarbon = Carbon::parse($fecha);
            if ($fechaCarbon->gte($inicioSemana)) {
                $minutosEstaSemana += $dia['total_minutos'];
            }
            if ($fechaCarbon->gte($inicioMes)) {
                $minutosEsteMes += $dia['total_minutos'];
            }
        }
        $horasEstaSemana = round($minutosEstaSemana / 60, 2);
        $horasEsteMes = round($minutosEsteMes / 60, 2);

        return view('admin.users.show', compact(
            'user',
            'fichajes',
            'horasPorDia',
            'horasEstaSemana',
            'horasEsteMes'
        ));
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,empleado',
            'pin' => 'required|size:4|unique:users,pin,' . $user->id,
            'password' => 'nullable|min:6',
            'horas_diarias' => 'required|numeric|min:1|max:24',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'pin' => $request->pin,
            'horas_diarias' => $request->horas_diarias,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        ActivityLog::registrar('usuario_actualizado', "Empleado actualizado: {$user->name} ({$user->email})", 'User', $user->id);
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminarte a ti mismo.');
        }

        ActivityLog::registrar('usuario_eliminado', "Empleado eliminado: {$user->name} ({$user->email})", 'User', $user->id);
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario eliminado correctamente.');
    }

    public function regenerarPin(User $user)
    {
        $pin = $this->generarPin();
        $user->update(['pin' => $pin]);

        ActivityLog::registrar('pin_regenerado', "PIN regenerado para: {$user->name}", 'User', $user->id);
        return back()->with('success', "PIN regenerado: {$pin}");
    }
}
