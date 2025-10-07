<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventMainResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'date'      =>  $this->date->format('Y-m-d g:i A'),
            'latitude'  => $this->latitude,
            'longitude' => $this->longitude,
            'number_of_guests' => $this->number_of_guests,
            'additional_details' => $this->additional_details??null


        ];
    }
}
