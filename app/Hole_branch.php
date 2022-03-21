<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Hole_branch extends Model
{
    protected $fillable = ['title_ar', 'title_en', 'latitude', 'longitude', 'hole_id'];
}
