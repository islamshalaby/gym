<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    protected $fillable = ['user_id', 'text', 'rate', 'type' , 'admin_approval','order_id'];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d',
    ];
    public function Hall() {
        return $this->belongsTo('App\Hole', 'order_id');
    }
}
