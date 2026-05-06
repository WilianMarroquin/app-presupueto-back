<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class BudgetItemDetailPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Budget Item Detailes',
            'Crear Budget Item Detailes',
            'Editar Budget Item Detailes',
            'Eliminar Budget Item Detailes',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'BudgetItemDetail',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::where('name', Rol::ADMINISTRADOR)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
