<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [
        'id'
    ];


    protected $casts = [
    'available_from' => 'datetime', 
    'available_to'   => 'datetime',
    'available_days' => 'array',
];


    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function image()
    {
        return $this->hasMany(ProductImage::class);
    }


    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function package()
    {
        return $this->belongsToMany(Package::class, 'package_products')
                    ->withTimestamps();
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'item_id')
            ->where('item_type', 'product');
    }

    public function options()
    {
        return $this->hasMany(ProductOption::class);
    }

 
    
}
