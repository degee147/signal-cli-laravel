<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;
    protected $fillable = ['sender', 'sender_name', 'receiver', 'receive_timestamp', 'delivered_timestamp', 'message_timestamp', 'body', 'replied'];
}
