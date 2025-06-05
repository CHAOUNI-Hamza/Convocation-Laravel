<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ExamResource;

class TeacherResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'sum_number' => $this->sum_number,
            'name' => $this->name,
            'first_name' => $this->first_name,
            'name_ar' => $this->name_ar,
            'first_name_ar' => $this->first_name_ar,
            'email' => $this->email,
            'city' => $this->city,
            'status' => $this->status,
            'limit' => $this->limit,
            'grad' => $this->grad,
            'day_time' => $this->day_time,
            'total_exams' => $this->exams->count(), // Nombre total d'examens
            'exams' => ExamResource::collection($this->exams) // Optionnel : afficher les examens
        ];
    }
}
