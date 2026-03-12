<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(['email' => 'admin@fichajes.com'], [
            'name' => 'Administrador',
            'password' => bcrypt('admin123'),
            'pin' => '0000',
            'role' => 'admin',
            'horas_diarias' => 8.0,
        ]);
    }
}
