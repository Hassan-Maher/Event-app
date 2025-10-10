<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $guarded = [
        'id'
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function event_items()
    {
        return $this->hasMany(EventItem::class , 'option_id');
    }
}
