<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_caoch_ask extends Model
{
    protected $fillable = [
        'user_id',
        'caoch_id',
        'ask_num_free',
        'ask_num_payed'
    ];
}
