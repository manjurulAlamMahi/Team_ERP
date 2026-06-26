<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientMessageType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'icon',
        'short_description',
        'format',
        'restriction',
        'mandatory',
        'status',
    ];

    public function messages()
    {
        return $this->hasMany(ClientMessage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
