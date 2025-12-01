<?php

namespace Database\Seeders\bases;

use App\Models\User;
use App\Models\UserEstado;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        User::firstOrCreate(
            ['email' => 'wilimarroquin61@gmail.com'],
            [
                'primer_nombre' => 'Wilian',
                'segundo_nombre' => 'Alberto',
                'primer_apellido' => 'Marroquin',
                'segundo_apellido' => 'Morales',
                'usuario' => 'wilian',
                'estado_id' => UserEstado::ACTIVO,
                'email' => 'admin@gmail.com',
                'password' => bcrypt('WILIAN.123.gt'),
            ]);
    }
}
