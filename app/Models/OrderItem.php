<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $guarded =[
        'id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

   public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
        
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'item_id');
       
    }
    


    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function option()
    {
        return $this->hasOne(ProductOption::class);
    }

}
