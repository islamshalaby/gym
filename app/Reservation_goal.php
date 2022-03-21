<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation_goal extends Model
{
    protected $fillable = ['title_ar', 'title_en','type_id','deleted','type'];

    public function Type() {
        return $this->belongsTo('App\Reservation_type', 'type_id');
    }
}
