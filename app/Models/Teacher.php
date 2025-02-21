<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Session;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'prenom', 'email'];

    public function sessions()
    {
        return $this->hasMany(Session::class, 'professeur_id');
    }
}
