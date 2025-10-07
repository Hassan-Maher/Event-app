<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'main_image'      => url($this->main_image),
            'price'           => $this->price ?? 'المنتج له اكثر من خيار',
            'description'     => $this->description,

            'service'         => new ServiceResource($this->whenLoaded('service')),
            'store'           => new StoreResource($this->whenLoaded('store')),
            'city'            => new CityResource($this->whenLoaded('city')),

            'available_days'  => (is_array($this->available_days) && count($this->available_days) == 7)
                                ? 'كل ايام الاسبوع'
                                : $this->available_days,

            'available_from'  => $this->available_from
                                ?  $this->available_from->format('g:i A')
                                : null,
            'available_to'    => $this->available_to
                                ?  $this->available_to->format('g:i A')
                                : null,

            'options' =>       $this->options? $this->whenLoaded('options')
            ->map(function($option){
                return [
                    'id' => $option->id,
                    'name' => $option->name,
                    'price' => $option->price
                ];
            }):null,

            'all_images'      => ProductImageResource::collection($this->whenLoaded('image')),
];

    }
}
