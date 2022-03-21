<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Coach extends Authenticatable implements JWTSubject
{
    protected $fillable = [
        'name',
        'name_en',
        'email',
        'password',
        'gender',
        'image',
        'fcm_token',
        'status',
        'deleted',
        'available',
        'famous',
        'verified',
        'about_coach',
        'time_from',
        'time_to',
        'rate',
        'sort',
        'age',
        'exp',
        'user_id',
        'phone',
        'about_coach_en',
        'story',
        'work_day_from',
        'work_day_to',
        'thumbnail'
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function Rates() {
        return $this->hasMany('App\Rate', 'order_id')->where('type','coach')->where('admin_approval',1);
    }

    protected $appends = ['coachname','about'];
    public function getCoachnameAttribute()
    {
        if ($locale = \app()->getLocale() == "ar") {
            return $this->name ;
        } else {
            return $this->name_en;
        }
    }
    public function getAboutAttribute()
    {
        if ($locale = \app()->getLocale() == "ar") {
            if($this->about_coach == null){
                return "" ;
            }else{
                return $this->about_coach ;
            }

        } else {
            if($this->about_coach_en == null){
                return "" ;
            }else{
                return $this->about_coach_en ;
            }
        }
    }

    public function Day_from() {
        return $this->belongsTo('App\Day', 'work_day_from')->select('id','name_'. session('api_lang') .' as name' );
    }
    public function Day_to() {
        return $this->belongsTo('App\Day', 'work_day_to')->select('id','name_'. session('api_lang') .' as name' );;
    }
}
