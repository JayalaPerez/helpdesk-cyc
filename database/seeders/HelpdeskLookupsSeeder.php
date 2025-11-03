<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\TicketCategory;

class HelpdeskLookupsSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Marketing',
            'RRHH',
            'Administración',
            'Informática',
            'Gerencia',
            'Psicología',
            'Control de gestión',
            'Diseño',
            'Comercial',
        ];

        foreach ($departments as $d) {
            Department::firstOrCreate(['name' => $d], ['is_active' => true]);
        }

        $categories = [
            'Correo',
            'Aplicación',
            'Hardware',
            'Software',
            'Otro',
            'Ingreso de Usuario',
            'Eliminación de Usuario',
        ];

        foreach ($categories as $c) {
            TicketCategory::firstOrCreate(['name' => $c], ['is_active' => true]);
        }
    }
}
