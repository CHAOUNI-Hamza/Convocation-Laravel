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
            'name_ar' => $this->faker->word, // À remplacer par des noms arabes si nécessaire
            'first_name_ar' => $this->faker->word, // À remplacer par des prénoms arabes si nécessaire
            'sum_number' => $this->faker->unique()->numerify('#######'), // Numéro unique à 7 chiffres
            'email' => $this->faker->unique()->safeEmail,
        ];
    }
}
