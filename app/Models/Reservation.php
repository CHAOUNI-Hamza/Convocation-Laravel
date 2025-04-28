<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Student;
use App\Models\Timeslot;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'timeslot_id',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function timeslot()
    {
        return $this->belongsTo(Timeslot::class);
    }

}
