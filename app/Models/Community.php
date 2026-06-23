<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Community extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'operation_manager_id',
        'status',
    ];

    public function operationManager()
    {
        return $this->belongsTo(User::class, 'operation_manager_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class);
    }
}
