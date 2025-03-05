<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TeacherResource;

class ExamResource extends JsonResource
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
            'date' => $this->date,
            'creneau_horaire' => $this->creneau_horaire,
            'module' => $this->module,
            'salle' => $this->salle,
            'filiere' => $this->filiere,
            'semestre' => $this->semestre,
            'groupe' => $this->groupe,
            'lib_mod' => $this->lib_mod,
            'teachers' => TeacherResource::collection($this->whenLoaded('teachers')),
        ];
    }
}
