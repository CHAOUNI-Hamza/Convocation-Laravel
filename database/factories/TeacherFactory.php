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
            'city' => $this->faker->city, // Ajout de la ville
            'status' => $this->faker->randomElement(['active', 'non']), // Ajout du statut (0 ou 1)
            'limit' => $this->faker->numberBetween(1, 100), // Ajout de la limite (nombre aléatoire)
            'grad' => $this->faker->randomElement(['Professeur', 'Maître de conférences', 'Assistant', 'Docteur']), // Ajout du grade
            'cycle' => $this->faker->lastName,
            'levels' => $this->faker->randomElement(['Licence', 'Master', 'Doctorat']),
            'num_student' => $this->faker->numberBetween(5, 100),
            'year' => $this->faker->randomElement(['2020/2021', '2021/2022', '2022/2023', '2023/2024']),
        ];
    }
}
