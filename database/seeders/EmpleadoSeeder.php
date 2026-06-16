<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Empleado;
use App\Models\User;

class EmpleadoSeeder extends Seeder
{
    public function run(): void
    {
        $userCajero   = User::where('email', 'cajero@elitemoda.com')->first();
        $userContable = User::where('email', 'contable@elitemoda.com')->first();

        $empleados = [
            [
                'user_id'             => $userCajero?->id,
                'cedula'              => '00112345680',
                'nombre'              => 'Carlos',
                'apellido'            => 'Pérez',
                'telefono'            => '8091234570',
                'cargo'               => 'Cajero',
                'salario_base'        => 25000,
                'comision_porcentaje' => 2,
                'fecha_ingreso'       => '2024-01-15',
            ],
            [
                'user_id'             => $userContable?->id,
                'cedula'              => '00112345681',
                'nombre'              => 'María',
                'apellido'            => 'González',
                'telefono'            => '8091234571',
                'cargo'               => 'Contable',
                'salario_base'        => 35000,
                'comision_porcentaje' => 0,
                'fecha_ingreso'       => '2023-06-01',
            ],
            [
                'user_id'             => null,
                'cedula'              => '00112345682',
                'nombre'              => 'Pedro',
                'apellido'            => 'Ramírez',
                'telefono'            => '8091234572',
                'cargo'               => 'Vendedor',
                'salario_base'        => 20000,
                'comision_porcentaje' => 3,
                'fecha_ingreso'       => '2024-03-10',
            ],
        ];

        foreach ($empleados as $data) {
            $empleado = Empleado::create(array_merge($data, ['estado' => true]));
            $empleado->codigo = 'EMP-' . str_pad($empleado->id, 3, '0', STR_PAD_LEFT);
            $empleado->save();
        }
    }
}