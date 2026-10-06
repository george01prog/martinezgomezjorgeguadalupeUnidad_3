<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Electrónica', 'Dispositivos y accesorios electrónicos'],
            ['Papelería', 'Artículos de oficina y escritorio'],
            ['Limpieza', 'Productos de limpieza e higiene'],
            ['Herramientas', 'Herramientas manuales y eléctricas'],
            ['Alimentos', 'Productos alimenticios no perecederos'],
            ['Mobiliario', 'Muebles y accesorios de oficina'],
        ];

        foreach ($categories as [$name, $description]) {
            Category::firstOrCreate(
                ['name' => $name],
                ['description' => $description, 'is_active' => true]
            );
        }
    }
}