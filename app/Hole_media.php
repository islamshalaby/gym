<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hole_media extends Model
{
    protected $fillable = ['image', 'type','hole_id', 'thumbnail'];
}
