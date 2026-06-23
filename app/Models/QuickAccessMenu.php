<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuickAccessMenu extends Model
{
    protected $fillable = [
        'user_id',
        'icon',
        'route',
        'url',
        'name',
    ];
}
