<?php

namespace Database\Factories;

use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->lastName,
            'first_name' => $this->faker->firstName,
            'name_ar' => $this->faker->lastName,
            'first_name_ar' => $this->faker->firstName,
            'sum_number' => $this->faker->unique()->numberBetween(1000, 9999),
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
