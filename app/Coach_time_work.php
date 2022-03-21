<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Coach_time_work extends Model
{
    protected $fillable = [
        'time_from',
        'time_to',
        'coach_id'
    ];
}
