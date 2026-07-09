<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'created_by',
        'username',
        'profile_id',
        'client_name',
        'country',
        'sales_man_name',
        'sales_man_whatsapp',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function profile()
    {
        return $this->belongsTo(FiverrProfile::class, 'profile_id');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'client_assignments')->withPivot('assigned_by')->withTimestamps();
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function isAssignedTo(User $user): bool
    {
        if ($this->relationLoaded('assignees')) {
            return $this->assignees->contains('id', $user->id);
        }

        return $this->assignees()->where('user_id', $user->id)->exists();
    }

    /**
     * Any current Leader/Co Leader/Stack Lead of the team may edit, team-scoped not creator-scoped.
     */
    public function isEditableBy(User $user): bool
    {
        return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']);
    }

    /**
     * Any current Leader/Co Leader/Stack Lead of the team may delete, team-scoped not creator-scoped.
     */
    public function isDeletableBy(User $user): bool
    {
        return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']);
    }
}
