<?php

namespace App\Http\Resources;

use App\Models\Product;
use App\Models\ProductOption;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use League\Uri\Idna\Option;

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
            'final_price' => $this->final_price,
            'store' => $this->when($this->relationLoaded('store') , new StoreResource($this->store)),
            'products' => $this->when($this->relationLoaded('product'), $this->product->map(function($p)
            {
                return [
                'product' => new ProductMainResource($p),
                'chosen_option'  => $this->when($p->pivot->option_id, new ProductOptionResource($p->options()->firstWhere('id' , $p->pivot->option_id))),
                ];
            })
        ),

        ];
    }
}
