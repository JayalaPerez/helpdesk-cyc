<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'user@cyc.cl'], // busca por email
            [
                'name' => 'Usuario CYC',
                'password' => Hash::make('password'), // clave: password
                'role' => 'user',
            ]
        );
    }
}
