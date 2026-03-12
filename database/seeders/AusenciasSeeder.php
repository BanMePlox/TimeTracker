<?php

namespace Database\Seeders;

use App\Models\Ausencia;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AusenciasSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = User::where('role', 'empleado')->get();

        if ($empleados->isEmpty()) {
            return;
        }

        // Limpiar ausencias existentes de empleados de prueba
        foreach ($empleados as $e) {
            Ausencia::where('user_id', $e->id)->delete();
        }

        $ausencias = [
            // Aprobadas (pasadas)
            [
                'user' => 0, 'tipo' => 'vacaciones',
                'inicio' => Carbon::today()->subDays(20), 'fin' => Carbon::today()->subDays(16),
                'estado' => 'aprobada', 'descripcion' => 'Vacaciones de invierno',
            ],
            [
                'user' => 1, 'tipo' => 'baja_medica',
                'inicio' => Carbon::today()->subDays(10), 'fin' => Carbon::today()->subDays(8),
                'estado' => 'aprobada', 'descripcion' => 'Gripe',
            ],
            [
                'user' => 2, 'tipo' => 'ausencia_justificada',
                'inicio' => Carbon::today()->subDays(5), 'fin' => Carbon::today()->subDays(5),
                'estado' => 'aprobada', 'descripcion' => 'Cita médica',
            ],
            // Pendientes
            [
                'user' => 0, 'tipo' => 'vacaciones',
                'inicio' => Carbon::today()->addDays(10), 'fin' => Carbon::today()->addDays(17),
                'estado' => 'pendiente', 'descripcion' => 'Vacaciones de verano',
            ],
            [
                'user' => 3, 'tipo' => 'ausencia_justificada',
                'inicio' => Carbon::today()->addDays(3), 'fin' => Carbon::today()->addDays(3),
                'estado' => 'pendiente', 'descripcion' => null,
            ],
            // Rechazada
            [
                'user' => 1, 'tipo' => 'ausencia_injustificada',
                'inicio' => Carbon::today()->subDays(30), 'fin' => Carbon::today()->subDays(29),
                'estado' => 'rechazada', 'descripcion' => null,
            ],
        ];

        foreach ($ausencias as $data) {
            $idx = $data['user'];
            if (!isset($empleados[$idx])) continue;

            Ausencia::create([
                'user_id'      => $empleados[$idx]->id,
                'tipo'         => $data['tipo'],
                'fecha_inicio' => $data['inicio'],
                'fecha_fin'    => $data['fin'],
                'estado'       => $data['estado'],
                'descripcion'  => $data['descripcion'],
            ]);
        }
    }
}
