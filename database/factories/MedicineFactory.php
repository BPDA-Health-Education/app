<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Medicine;

class MedicineFactory extends Factory
{
    protected $model = Medicine::class;

    public function definition()
    {
        $forms = ['tablet', 'syrup', 'injection', 'capsule', 'ointment'];

        return [
            'name' => ucfirst($this->faker->unique()->words(2, true)),
            'generic_name' => $this->faker->word(),
            'form' => $this->faker->randomElement($forms),
            'is_active' => $this->faker->boolean(90),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
