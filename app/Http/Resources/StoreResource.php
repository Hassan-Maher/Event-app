<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'logo'              => $this->logo ? url($this->logo) : null,
            'commercial_number' => $this->commercial_number,
            'latitude'          => $this->latitude,
            'longitude'         => $this->longitude,
            'provider'          => new UserResource($this->whenLoaded('provider')),
            'products'          =>  ProductMainResource::collection($this->whenLoaded('product'))
        ];
    }
}
