<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exam>
 */
class ExamFactory extends Factory
{
    protected $model = Exam::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'date' => $this->faker->date(),
            'creneau_horaire' => $this->faker->time('H:i'), // Format 00:00
            'module' => $this->faker->word(),
            'salle' => 'Salle ' . $this->faker->numberBetween(1, 20),
            'filiere' => $this->faker->randomElement(['Informatique', 'Gestion', 'Mathématiques']),
            'semestre' => 'S' . $this->faker->numberBetween(1, 6),
            'groupe' => 'G' . $this->faker->numberBetween(1, 10),
            'lib_mod' => $this->faker->sentence(3),
        ];
    }
}
