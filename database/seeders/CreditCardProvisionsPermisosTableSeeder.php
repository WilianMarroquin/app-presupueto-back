<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class CreditCardProvisionsPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Credit Card Provisionses',
            'Crear Credit Card Provisionses',
            'Editar Credit Card Provisionses',
            'Eliminar Credit Card Provisionses',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'CreditCardProvisions',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
