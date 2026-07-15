<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Rol;
use Illuminate\Database\Seeder;

class FixedExpensePermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Listar Fixed Expenses',
            'Ver Fixed Expenses',
            'Crear Fixed Expenses',
            'Editar Fixed Expenses',
            'Eliminar Fixed Expenses',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'FixedExpense',
                'guard_name' => 'web',
            ]);
        }

        $admin = Rol::where('name', Rol::ADMINISTRADOR)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
