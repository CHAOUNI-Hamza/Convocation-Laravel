<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'creneau_horaire', 'module', 'salle', 'filiere', 'semestre', 'groupe', 'lib_mod', 'prof_mod'];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'exam_teacher')->withTimestamps();
    }
}
