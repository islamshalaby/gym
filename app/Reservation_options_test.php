<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reservation_options_test extends Model
{
    protected $fillable = ['option_id','value','bill_num','is_done'];
}
