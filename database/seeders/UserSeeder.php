<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrador
        User::updateOrCreate(
            ['email' => 'admin@sigerd.com'],
            [
                'name' => 'Administrador',
                'password' => Hash::make('password123'),
                'role' => 'administrador',
            ]
        );

        // Trabajador
        User::updateOrCreate(
            ['email' => 'trabajador@sigerd.com'],
            [
                'name' => 'Trabajador',
                'password' => Hash::make('password123'),
                'role' => 'trabajador',
            ]
        );

        // Instructor
        User::updateOrCreate(
            ['email' => 'instructor@sigerd.com'],
            [
                'name' => 'Instructor',
                'password' => Hash::make('password123'),
                'role' => 'instructor',
            ]
        );
    }
}
