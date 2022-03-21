<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Session;

class Reservation_type extends Model
{
    protected $fillable = ['title_ar', 'title_en','deleted','is_required'];

    public function Goals() {
        if(Session::get('api_lang') == 'ar'){
            return $this->hasMany('App\Reservation_goal', 'type_id')->select('id','type_id','title_ar as title');
        }else{
            return $this->hasMany('App\Reservation_goal', 'type_id')->select('id','type_id','title_en as title');
        }
    }
}
