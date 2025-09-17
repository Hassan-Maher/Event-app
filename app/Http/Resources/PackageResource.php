<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description? $this->description:null,
            'image_path' => url($this->image),
            'price' => $this->price,
            'offer' => $this->offer? $this->offer . '%' : null,
            'price_after_offer' => $this->offer?($this->price * $this->offer) /100 : $this->price,
            'store' => new StoreResource($this->whenLoaded('store')),
            'products' => ProductMainResource::collection($this->whenLoaded('product')),

        ];
    }
}
