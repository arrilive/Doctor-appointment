<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamar al RoleSeeder
        $this->call([
            RoleSeeder::class
        ]);

        //Crea un usuario de prueba cada que ejecuto migrations
       User::firstOrCreate(
        ['email' => 'luis@example.com'], 
        [
            'name' => 'Luis Vera',
            'password' => bcrypt('12345678'),
        ]
    );

    }
}
