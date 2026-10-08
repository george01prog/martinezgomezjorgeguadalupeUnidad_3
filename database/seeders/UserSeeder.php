<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@inventario.test'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('Admin12345'),
                'role' => User::ROLE_ADMIN,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'usuario@inventario.test'],
            [
                'name' => 'Usuario Demo',
                'password' => Hash::make('Usuario12345'),
                'role' => User::ROLE_USER,
                'is_active' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'inactivo@inventario.test'],
            [
                'name' => 'Usuario Inactivo',
                'password' => Hash::make('Inactivo12345'),
                'role' => User::ROLE_USER,
                'is_active' => false,
            ]
        );
    }
}