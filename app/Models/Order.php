<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [
        'id'
    ];

    

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function calculatePrice()
    {
        $total = 0;

        foreach ($this->items as $item) 
        {
            $total+= $item->price * $item->quantity;
        }
        return $total;
    }
    public function calculateNewPrice()
    {
        $total = 0;

        foreach ($this->items as $item) {
            if($item->status == 'accepted')
            {
                $total += $item->price * $item->quantity;
            }
            
        }

        return $total;
    }
}
