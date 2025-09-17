<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [
        'id'
    ];

     public function product()
    {
        return $this->belongsToMany(Product::class, 'package_products')
                    ->withTimestamps();
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }


}
