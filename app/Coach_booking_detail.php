<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coach_booking_detail extends Model
{
    protected $fillable = ['name_ar','name_en', 'booking_id'];

    public function Booking() {
        return $this->belongsTo('App\Coach_booking', 'booking_id');
    }
}
