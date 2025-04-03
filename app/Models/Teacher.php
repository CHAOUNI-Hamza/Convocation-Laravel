<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Exam;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'first_name', 'name_ar', 'first_name_ar', 'sum_number', 'email', 'city', 'status', 'limit'];

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_teacher')->withTimestamps();
    }

}
