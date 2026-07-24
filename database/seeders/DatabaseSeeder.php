<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Order matters for foreign keys
        $this->call([
            UsersTableSeeder::class,
            MedicinesTableSeeder::class,
        ]);
    }
}
