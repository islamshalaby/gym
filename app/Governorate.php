<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Governorate extends Model
{
    protected $fillable = ['title_en', 'title_ar', 'deleted'];

    public function areas() {
        if (session('api_lang') == 'en') {
            return $this->hasMany('App\Area','governorate_id', 'id')->where('deleted','0')
                ->select('id' , 'title_en as title','governorate_id');
        }else{
            return $this->hasMany('App\Area','governorate_id', 'id')->where('deleted','0')
                ->select('id' , 'title_ar as title','governorate_id');
        }
    }
}
