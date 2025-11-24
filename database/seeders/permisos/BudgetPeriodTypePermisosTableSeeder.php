<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class BudgetPeriodTypePermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Budget Period Types',
            'Crear Budget Period Types',
            'Editar Budget Period Types',
            'Eliminar Budget Period Types',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'BudgetPeriodType',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
