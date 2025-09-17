<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = [
        'id'
    ];

    protected $casts = [
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

 
    
}
