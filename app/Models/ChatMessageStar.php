<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessageStar extends Model
{
    protected $fillable = ['message_id', 'admin_id'];
}
