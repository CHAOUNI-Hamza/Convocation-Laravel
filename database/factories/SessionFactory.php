<?php

namespace Database\Factories;

use App\Models\Session;
use App\Models\Teacher;
use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Session>
 */
class SessionFactory extends Factory
{
    protected $model = Session::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'professeur_id' => Teacher::factory(),
            'examen_id' => Exam::factory(),
            'date' => $this->faker->date,
            'creneau_horaire' => $this->faker->time(),
        ];
    }
}
