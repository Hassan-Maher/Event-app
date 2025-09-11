<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
   protected $fillable = [
        'name',
        'logo',
        'commercial_number',
        'latitude',
        'longitude',
        'city_id',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
