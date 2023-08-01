<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessageRecipient extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uid',
        'recipient_id',
        'chat_message_id',
        'device_message_id'
    ];

}
