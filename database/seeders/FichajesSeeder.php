<?php

namespace Database\Seeders;

use App\Models\Fichaje;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class FichajesSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = User::where('role', 'empleado')->get();

        // Limpiar fichajes existentes de los empleados de prueba
        foreach ($empleados as $empleado) {
            Fichaje::where('user_id', $empleado->id)->delete();
        }

        foreach ($empleados as $empleado) {
            // Generar fichajes para los últimos 30 días hábiles
            $diasGenerados = 0;
            $dia = Carbon::today()->subDays(1);

            while ($diasGenerados < 30) {
                // Saltar fines de semana
                if ($dia->isWeekend()) {
                    $dia->subDay();
                    continue;
                }

                // 10% de probabilidad de ausencia (sin fichaje)
                if (rand(1, 10) === 1) {
                    $dia->subDay();
                    $diasGenerados++;
                    continue;
                }

                // Hora de entrada: entre 8:00 y 9:30
                $entradaHora = rand(480, 570); // minutos desde medianoche
                $entradaMinutos = rand(0, 59);

                $entrada = $dia->copy()
                    ->setHour(intdiv($entradaHora, 60))
                    ->setMinute($entradaMinutos)
                    ->setSecond(0);

                // Calcular horas trabajadas según jornada del empleado (±30min de variación)
                $jornadaMinutos = $empleado->horas_diarias * 60;
                $variacion = rand(-30, 30);
                $salidaMinutos = $jornadaMinutos + $variacion;

                $salida = $entrada->copy()->addMinutes($salidaMinutos);

                Fichaje::create([
                    'user_id'    => $empleado->id,
                    'tipo'       => 'entrada',
                    'created_at' => $entrada,
                    'updated_at' => $entrada,
                ]);

                Fichaje::create([
                    'user_id'    => $empleado->id,
                    'tipo'       => 'salida',
                    'created_at' => $salida,
                    'updated_at' => $salida,
                ]);

                $dia->subDay();
                $diasGenerados++;
            }
        }
    }
}
