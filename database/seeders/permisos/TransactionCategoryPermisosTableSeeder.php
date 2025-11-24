<?php

namespace Database\Seeders\permisos;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class TransactionCategoryPermisosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permisos = [
            'Ver Transaction Categoryes',
            'Crear Transaction Categoryes',
            'Editar Transaction Categoryes',
            'Eliminar Transaction Categoryes',
        ];

        foreach ($permisos as $permiso) {
            Permission::create([
                'name' => $permiso,
                'subject' => 'TransactionCategory',
                'guard_name' => 'api',
            ]);
        }

        $admin = Role::where('name', Role::ADMIN)
            ->first();

        $admin->givePermissionTo($permisos);

    }

}
