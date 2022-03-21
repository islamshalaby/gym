<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = ['visitor_id', 'product_id', 'option_id', 'count', 'store_id'];

    public function product() {
        return $this->belongsTo('App\Product', 'product_id');
    }
}
