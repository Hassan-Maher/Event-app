<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'price' => $this->price,
            'offer' => $this->offer,
            'final_price' => $this->final_price,
            'payment_method' => $this->payment_method,
            'status' => $this->status,
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'user' => new UserResource($this->whenLoaded('user')),
            'items' =>  ItemsResource::collection($this->whenLoaded('items'))



        ];
    }
}
