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
            'item_type'     => $this->item_type,
            'item_id'       => $this->item_id,
            'item_status'   => $this->status,
            'item_details'  => ($this->item_type == 'product') ? new ProductMainResource($this->whenLoaded('product')) : new PackageResource($this->whenLoaded('package')),
            'option'        => $this->whenLoaded('option'),
            'rejected reason_if item rejected' => $this->status == 'rejected'? $this->rejected_reason:'الطلب ليس بمرفوض',
            'order' => new OrderResource($this->whenLoaded('order'))
        ];
    }
}
