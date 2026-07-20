<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $rolAdmin    = Rol::where('nombre', 'Administrador')->first();
        $rolCajero   = Rol::where('nombre', 'Cajero')->first();
        $rolContable = Rol::where('nombre', 'Contable')->first();

        // Usuario administrador principal
        User::create([
            'name'     => 'Administrador',
            'email'    => 'admin@elitemoda.com',
            'password' => Hash::make('EliteModaADMIN'),
            'rol_id'   => $rolAdmin?->id,
            'estado'   => true,
        ]);

        // Usuario cajero de prueba
        User::create([
            'name'     => 'Cajero Demo',
            'email'    => 'cajero@elitemoda.com',
            'password' => Hash::make('EliteModaCAJERO'),
            'rol_id'   => $rolCajero?->id,
            'estado'   => true,
        ]);

        // Usuario contable de prueba
        User::create([
            'name'     => 'Contable Demo',
            'email'    => 'contable@elitemoda.com',
            'password' => Hash::make('EliteModaCONTABLE'),
            'rol_id'   => $rolContable?->id,
            'estado'   => true,
        ]);
    }
}