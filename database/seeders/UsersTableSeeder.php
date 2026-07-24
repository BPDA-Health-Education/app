<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create a Super Admin
        $admin = User::factory()->admin()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'phone' => '01700000001',
            'password' => Hash::make('AdminPass123!'),
        ]);

        // Create a Doctor
        $doctor = User::factory()->doctor()->create([
            'name' => 'Dr. A. Khan',
            'email' => 'doctor@example.com',
            'phone' => '01700000002',
            'password' => Hash::make('DoctorPass123!'),
        ]);

        // Create several Health Workers and assign them to the doctor
        $healthWorkers = User::factory()->count(6)->healthWorker()->create();

        foreach ($healthWorkers as $hw) {
            DB::table('doctor_assignments')->insert([
                'doctor_id' => $doctor->id,
                'health_worker_id' => $hw->id,
                'assigned_at' => now(),
            ]);
        }

        // Additional sample users
        User::factory()->count(3)->create();
    }
}
