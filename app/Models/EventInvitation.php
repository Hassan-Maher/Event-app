<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventInvitation extends Model
{
    protected $guarded = [
        'id'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function inviter()
    {
        return $this->belongsTo(User::class , 'inviter_id');
    }
    
    public function invitee()
    {
        return $this->belongsTo(User::class , 'invitee_id');
    }
}
