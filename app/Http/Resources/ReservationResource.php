<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
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
            'apogee'     => $this->student->apogee,
            'cne'        => $this->student->cne,
            'first_name'=> $this->student->first_name,
            'last_name' => $this->student->last_name,
            'lab'       => $this->student->lab,
            'cnie'      => $this->student->cnie,
            'date'      => $this->timeslot->date,
            'email'      => $this->timeslot->email,
            'time_range'=> $this->timeslot->time_range,
        ];
    }
}
