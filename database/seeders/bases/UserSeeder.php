<?php

namespace Database\Seeders\bases;

use App\Models\User;
use App\Models\UserEstado;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Para ejecutar este seeder: php artisan db:seed --class="Database\Seeders\bases\UserSeeder"
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
                'usuario' => 'admin',
                'estado_id' => UserEstado::ACTIVO,
                'email' => 'wilimarroquin61@gmail.com',
                'password' => bcrypt('12354'),
            ]);
    }
}
