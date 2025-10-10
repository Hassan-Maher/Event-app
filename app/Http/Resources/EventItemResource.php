<?php

namespace App\Http\Resources;

use App\Models\ProductOption;
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
            'type' => $this->type,
            'price' => $this->price,
            'item_details' => $this->when($this->relationLoaded('product') ||$this->relationLoaded('package') ,
                $this->type == 'product'? 
            [
            'product' => new ProductMainResource($this->product),
            'chosen_option' =>$this->when($this->option_id,  new ProductOptionResource($this->option)),
            ]
            :
            new PackageResource($this->package)
            ),
        ];
    }
}
