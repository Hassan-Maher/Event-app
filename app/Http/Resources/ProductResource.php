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
            'price'           => $this->price ?? null,
            'description'     => $this->description,

            'service'         => new ServiceResource($this->whenLoaded('service')),
            'store'           => new StoreResource($this->whenLoaded('store')),
            'city'            => new CityResource($this->whenLoaded('city')),

            'available_days'  => (is_array($this->available_days) && count($this->available_days) == 7)
                                ? 'كل ايام الاسبوع'
                                : $this->available_days,

            'available_from'  => $this->available_from
                                ? Carbon::createFromFormat('H:i:s', $this->available_from)->format('g:i A')
                                : null,
            'available_to'    => $this->available_to
                                ? Carbon::createFromFormat('H:i:s', $this->available_to)->format('g:i A')
                                : null,

            'first_option'        => $this->first_option ?? null,
            'first_option_price'  => $this->first_price ?? null,

            'second_option'       => $this->second_option ?? null,
            'second_option_price' => $this->second_price ?? null,

            'third_option'        => $this->third_option ?? null,
            'third_option_price'  => $this->third_price ?? null,

            'all_images'      => ProductImageResource::collection($this->whenLoaded('image')),
];

    }
}
