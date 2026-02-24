<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SpecialitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialities = [
            'Cardiología',
            'Pediatría',
            'Dermatología',
            'Neurología',
            'Ginecología',
            'Traumatología',
            'Oftalmología',
            'Psiquiatría',
        ];
        foreach ($specialities as $speciality) {
            \App\Models\Speciality::firstOrCreate(['name' => $speciality]);
        }
    }
}
