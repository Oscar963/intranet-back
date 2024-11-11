<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::create([
            'nombre' => 'Perico',
            'rut' => '24866295-6',
            'apellido_materno' => 'user2@example.com',
            'apellido_paterno' => 'admin@example.com',
            'email' => 'oscar.apata01@gmail.com',
            'estado' => 0,
            'password' => Hash::make('password'),
        ]);

        $admin->roles()->attach(2);

        $admin2 = User::create([
            'nombre' => 'Oscar',
            'rut' => '13689472-2',
            'apellido_paterno' => 'Apata',
            'apellido_materno' => 'Tito',
            'email' => 'oscar.apata@municipalidadarica.cl',
            'estado' => 1,
            'password' => Hash::make('password'),
        ]);

        $admin2->roles()->attach(1);
    }
}
