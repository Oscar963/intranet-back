<?php

namespace Database\Seeders;

use App\Models\Registro;
use App\Models\User;
use App\Models\Vehiculo;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear 10 usuarios
        //$users = User::factory(100)->create();

        // Crear 10 vehículos
        // Vehiculo::factory(10)->create()->each(function ($vehiculo) use ($users) {
        //     // Crear entre 1 y 10 registros para cada vehículo
        //     Registro::factory(rand(1, 10))->create([
        //         'vehiculo_id' => $vehiculo->id,
        //         'entrada_user_id' => $users->random()->id,
        //         'salida_user_id' => function (array $attributes) use ($users) {
        //             // Generar un usuario aleatorio para la salida si la salida no es nula
        //             return $attributes['salida'] ? $users->random()->id : null;
        //         },
        //     ]);
        // });

        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
