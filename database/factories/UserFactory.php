<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $role = $this->faker->randomElement(['HEALTH_WORKER', 'DOCTOR', 'ADMIN']);
        $status = $this->faker->randomElement(['PENDING', 'ACTIVE', 'SUSPENDED']);

        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->unique()->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'), // Change in production
            'role' => $role,
            'status' => $status,
            'can_write_prescription' => $role === 'HEALTH_WORKER' ? true : true,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Health worker state.
     */
    public function healthWorker()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'HEALTH_WORKER',
                'status' => 'ACTIVE',
                'can_write_prescription' => true,
            ];
        });
    }

    /**
     * Doctor state.
     */
    public function doctor()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'DOCTOR',
                'status' => 'ACTIVE',
                'can_write_prescription' => true,
            ];
        });
    }

    /**
     * Admin state.
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'ADMIN',
                'status' => 'ACTIVE',
                'can_write_prescription' => true,
            ];
        });
    }
}
