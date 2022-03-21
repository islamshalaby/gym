<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = ['message', 'type', 'is_read','user_id','user_type','conversation_id','coach_id'];
}
