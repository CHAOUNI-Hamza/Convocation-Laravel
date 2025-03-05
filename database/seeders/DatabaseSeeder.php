<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Exam;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->count(10)->create();
        
        $teachers = Teacher::factory(20)->create();
        $exams = Exam::factory(10)->create();

        // Affecter les enseignants aux examens
        foreach ($exams as $exam) {
            // Sélectionner entre 1 et 3 enseignants pour chaque examen
            $exam->teachers()->attach(
                $teachers->random(rand(1, 3))->pluck('id')->toArray()
            );
        }

    }
}
