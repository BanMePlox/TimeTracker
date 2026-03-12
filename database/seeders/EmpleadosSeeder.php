<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class EmpleadosSeeder extends Seeder
{
    public function run(): void
    {
        $empleados = [
            ['name' => 'Ana García López',     'email' => 'ana@timetrack.test',    'pin' => '1111', 'horas_diarias' => 8.0],
            ['name' => 'Carlos Martínez Ruiz', 'email' => 'carlos@timetrack.test', 'pin' => '2222', 'horas_diarias' => 6.0],
            ['name' => 'Laura Sánchez Pérez',  'email' => 'laura@timetrack.test',  'pin' => '3333', 'horas_diarias' => 8.0],
            ['name' => 'David Torres Vega',    'email' => 'david@timetrack.test',  'pin' => '4444', 'horas_diarias' => 7.5],
            ['name' => 'Elena Romero Gil',     'email' => 'elena@timetrack.test',  'pin' => '5555', 'horas_diarias' => 4.0],
        ];

        foreach ($empleados as $data) {
            User::firstOrCreate(['email' => $data['email']], [
                'name'          => $data['name'],
                'password'      => bcrypt('empleado123'),
                'pin'           => $data['pin'],
                'role'          => 'empleado',
                'horas_diarias' => $data['horas_diarias'],
            ]);
        }
    }
}
