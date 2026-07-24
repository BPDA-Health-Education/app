<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicine;

class MedicinesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create some curated medicines
        $curated = [
            ['name' => 'Paracet Plus', 'generic_name' => 'Paracetamol', 'form' => 'tablet', 'is_active' => true],
            ['name' => 'Amoxi 500', 'generic_name' => 'Amoxicillin', 'form' => 'capsule', 'is_active' => true],
            ['name' => 'Cough Syrup', 'generic_name' => 'Dextromethorphan', 'form' => 'syrup', 'is_active' => true],
            ['name' => 'Salbutamol Inhaler', 'generic_name' => 'Salbutamol', 'form' => 'inhaler', 'is_active' => true],
            ['name' => 'Vitamin B Complex', 'generic_name' => 'B-Complex', 'form' => 'tablet', 'is_active' => true],
        ];

        foreach ($curated as $m) {
            Medicine::create(array_merge($m, ['created_at' => now(), 'updated_at' => now()]));
        }

        // Fill with additional random medicines
        Medicine::factory()->count(30)->create();
    }
}
