<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
   protected $guarded = [
      'id'
    ];

    public function provider()
    {
        return $this->belongsTo(User::class , 'user_id');
    }

    public function package()
    {
        return $this->hasMany(Package::class);
    }

     public function product()
    {
        return $this->hasMany(Product::class);
    }
    
}
