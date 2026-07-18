<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'created_by',
        'title',
        'link',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Any current Leader/Co Leader of the team may edit or delete, team-scoped not creator-scoped.
     */
    public function isEditableBy(User $user): bool
    {
        return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader']);
    }
}
