<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;
use App\Models\Permission;

class BudgetPeriodPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Budget Periodes',
            'Crear Budget Periodes',
            'Editar Budget Periodes',
            'Eliminar Budget Periodes',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'BudgetPeriod',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::where('name', Rol::ADMINISTRADOR)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
