<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientMessageAttachment extends Model
{
    protected $fillable = [
        'client_message_id',
        'type',
        'original_name',
        'path',
    ];

    public function message()
    {
        return $this->belongsTo(ClientMessage::class, 'client_message_id');
    }
}
