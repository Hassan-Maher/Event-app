<?php

namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'quantity'      => $this->quantity,
            'price'         => $this->price,
            'type'     => $this->type,
            'item_id'       => $this->item_id,
            'item_status'   => $this->status,
            'rejected reason_if item rejected' => $this->when($this->status == 'rejected', $this->rejected_reason),
            'item_details' => $this->when($this->relationLoaded('product') ||$this->relationLoaded('package') ,
                $this->type == 'product'? 
            [
            'product' => new ProductMainResource($this->product),
            'chosen_option' => $this->when($this->option_id ,new ProductOptionResource($this->option)),
            ]
            :
            new PackageResource($this->package)
            ),

        ];
    }
}
