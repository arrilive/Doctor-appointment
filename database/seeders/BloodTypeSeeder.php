<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BloodTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bloodTypes = ['A-', 'A+', 'AB-', 'AB+','B-', 'B+', 'O-', 'O+'];

        foreach ($bloodTypes as $BloodType) {
            \App\Models\BloodType::firstOrCreate(['name' => $BloodType]);
        }
    }
}
