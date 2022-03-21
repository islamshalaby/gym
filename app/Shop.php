<?php

namespace App;

use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Shop extends Authenticatable implements JWTSubject
{
    protected $fillable = ['logo','cover','name_ar','name_en','desc_ar','desc_en','phone','email','password','fcm_token','famous','status','min_order_cost'];

    protected $appends = ['name'];
    public function getNameAttribute()
    {
        if ($locale = \app()->getLocale() == "ar") {
            return $this->name_ar ;
        } else {
            return $this->name_en;
        }
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
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

    public function products() {
        return $this->hasMany('App\Product', 'store_id');
    }

    public function categories() {
        return $this->belongsToMany('App\Category', 'stores_categories', 'store_id', 'category_id');
    }

    public function seller() {
        return $this->belongsTo('App\Seller', 'seller_id');
    }

    public function areas() {
        return $this->hasMany('App\DeliveryArea', 'store_id');
    }

    public function deliveryByarea($area) {
        $data = $this->hasOne('App\DeliveryArea', 'store_id')->where('area_id', $area)->first();

        return $this->hasOne('App\DeliveryArea', 'store_id')->where('area_id', $area)->first();
    }

}
