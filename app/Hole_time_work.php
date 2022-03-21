<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hole_time_work extends Model
{
    protected $fillable = [
        'time_from',
        'time_to',
        'hole_id',
        'type',
    ];
}
