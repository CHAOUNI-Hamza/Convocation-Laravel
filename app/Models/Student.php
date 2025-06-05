<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reservation;

class Student extends Model
{
    use HasFactory;

    protected $fillable = ['apogee', 'cne', 'first_name', 'last_name', 'last_name_ar', 'first_name_ar', 'cnie', 'birth_date', 'lab', 'email', 'tel'];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
