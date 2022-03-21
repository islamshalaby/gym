<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = ['user_id','other_user_id', 'conversation_id', 'coach_id','user_type','other_user_type','deleted'];

    public function Ad_product()
    {
        return $this->belongsTo('App\AdProduct', 'ad_product_id');
    }
    public function User()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function Coach()
    {
        return $this->belongsTo('App\Coach', 'user_id');
    }
    public function Conversation()
    {
        return $this->belongsTo('App\Conversation', 'conversation_id');
    }
}
