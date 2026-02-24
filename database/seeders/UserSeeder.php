<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Crea un usuario de prueba cada que ejecuto migrations
        $doctor = User::firstOrCreate([
            'email' => 'luis@example.com',
            'name' => 'Luis Vera',
            'password' => bcrypt('12345678'),
            'id_number' => '123456789',
            'phone' => '7777777777',
            'address' => 'Calle 123, Gran Santa Fe',
            ]);
            
        $doctor->assignRole('Doctor');
        if (!$doctor->doctor) {
            $doctor->doctor()->create();
        }
    }
}
