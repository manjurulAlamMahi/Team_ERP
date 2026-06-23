<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'community_id',
        'name',
        'started_at',
        'description',
        'status',
    ];

    protected $casts = [
        'started_at' => 'date',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function members()
    {
        return $this->hasMany(User::class);
    }
}
