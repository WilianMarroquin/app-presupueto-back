<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class BudgetItemPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Budget Itemes',
            'Crear Budget Itemes',
            'Editar Budget Itemes',
            'Eliminar Budget Itemes',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'BudgetItem',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::where('name', Rol::ADMINISTRADOR)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
