<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = ['reservation_id', 'type','price','user_id','booking_id','payment'];

    public function User() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function Booking() {
        return $this->belongsTo('App\Hole_booking', 'booking_id');
    }

    public function Booking_coach() {
        return $this->belongsTo('App\Coach_booking', 'booking_id');
    }

}
