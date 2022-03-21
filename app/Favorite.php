<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['user_id', 'product_id','type'];

    public function User() {
        return $this->belongsTo('App\User', 'user_id');
    }
    public function Hall() {
        return $this->belongsTo('App\Hole', 'product_id');
    }

    public function Coach() {
        return $this->belongsTo('App\Coach', 'product_id');
    }
    public function Shop() {
        return $this->belongsTo('App\Shop', 'product_id');
    }

    public function Product() {
        return $this->belongsTo('App\Product', 'product_id')->where('deleted', 0)->where('hidden', 0);
    }
}
