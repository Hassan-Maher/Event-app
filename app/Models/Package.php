<?php

namespace App\Models;

use GuzzleHttp\Handler\Proxy;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
    'end_date' => 'datetime', 
    
];

    public function product()
    {
        return $this->belongsToMany(Product::class, 'package_products')
                    ->withPivot('option_id')
                    ->withTimestamps();
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'item_id')
            ->where('item_type', 'package');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items' , 'item_id' , 'order_id')
            ->where('item_type', 'package');
    }


    public function event_Items()
    {
        return $this->hasMany(EventItem::class, 'item_id')
            ->where('item_type', 'package');
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_items' , 'item_id' , 'event_id')
            ->where('item_type', 'package');
    }



    public function calculate_price()
    {
        $price = 0;
        foreach($this->product as $product)
        {
            $test_product = Product::find($product['id']);

            if(!empty($product->pivot->option_id))
            {
                $option = ProductOption::find($product->pivot->option_id);
                $price+= $option->price;
                continue;
            }

            $price+= $test_product->price;
        }
        return $price;
    }

}
