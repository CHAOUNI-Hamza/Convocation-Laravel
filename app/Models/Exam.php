<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Session;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['date', 'creneau_horaire', 'module', 'salle', 'filiere', 'semestre', 'groupe', 'lib_mod', 'teacher_id'];

    protected $casts = [
        'teacher_ids' => 'array', // Convertit automatiquement JSON en tableau PHP
    ];
}
