<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['title_ar', 'title_en','desc_ar','desc_en' ,'image','type','content','place'];
    protected $appends = ['title','desc'];
    public function getTitleAttribute()
    {
        if ($locale = \app()->getLocale() == "ar") {
            return $this->title_ar ;
        } else {
            return $this->title_en;
        }
    }

    public function getDescAttribute()
    {
        if ($locale = \app()->getLocale() == "ar") {
            return $this->desc_ar ;
        } else {
            return $this->desc_en;
        }
    }


    public function Coach() {
        return $this->belongsTo('App\Coach', 'content');
    }
    public function Hall() {
        return $this->belongsTo('App\Hole', 'content');
    }
}
