<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded =[
        'id'
    ];

    protected $casts = [
    'date' => 'datetime', 
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function images()
    {
        return $this->hasMany(EventImage::class);
    }

    public function tasks()
    {
        return $this->hasMany(EventTask::class);
    }
    public function items()
    {
        return $this->hasMany(EventItem::class);
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

    public function invitations()
    {
        return $this->hasMany(EventInvitation::class);
    }

}
