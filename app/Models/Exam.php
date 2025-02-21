<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Session;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = ['matiere', 'date', 'creneau_horaire', 'salle'];

    public function sessions()
    {
        return $this->hasMany(Session::class, 'examen_id');
    }
}
