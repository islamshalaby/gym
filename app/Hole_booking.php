<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class Hole_booking extends Model
{
    protected $fillable = ['name_ar','name_en','title_ar', 'title_en','price','discount','is_discount','discount_price','common','deleted','hole_id','months_num'];

    public function Details() {
        if(Session::get('api_lang') == 'ar'){
            return $this->hasMany('App\Hole_booking_detail', 'booking_id','id')->select('id','name_ar as name','booking_id');
        }else{
            return $this->hasMany('App\Hole_booking_detail', 'booking_id','id')->select('id','name_en as name','booking_id');
        }
    }

    public function Hall() {
        return $this->belongsTo('App\Hole', 'hole_id');
    }
}
