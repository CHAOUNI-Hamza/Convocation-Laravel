<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

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
            'date' => Carbon::now()->addDays(rand(1, 30))->format('Y-m-d'),
            'creneau_horaire' => $this->faker->randomElement(['09:00', '11:00', '14:00', '16:30']),
            'module' => $this->faker->word,
            'salle' => 'Salle ' . rand(1, 10),
            'filiere' => $this->faker->randomElement(['Informatique', 'Mathématiques', 'Physique']),
            'semestre' => 'Semestre ' . rand(1, 6),
            'groupe' => 'Groupe ' . rand(1, 5),
            'lib_mod' => $this->faker->word,
            'prof_mod' => $this->faker->name,
        ];
    }
}
