<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventItem extends Model
{
    protected $guarded = [
        'id'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
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

}
