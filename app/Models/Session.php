<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Teacher;
use App\Models\Exam;

class Session extends Model
{
    use HasFactory;

    protected $fillable = ['professeur_id', 'examen_id', 'date', 'creneau_horaire'];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'professeur_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'examen_id');
    }
}
