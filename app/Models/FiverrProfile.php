<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiverrProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'status',
    ];

    public function clients()
    {
        return $this->hasMany(Client::class, 'profile_id');
    }
}
