<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'price' => $this->price,
            'item_details' => $this->item_type == 'product'? new ProductMainResource($this->whenLoaded('product')): new PackageResource($this->whenLoaded('package')),
        ];
    }
}
