<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'created_by',
        'last_edited_by',
        'issue_date',
        'client_name',
        'profile_name',
        'issue',
        'type',
        'status',
        'completed_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lastEditor()
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function completer()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function responsibles()
    {
        return $this->belongsToMany(User::class, 'daily_issue_responsibles');
    }

    public function comments()
    {
        return $this->hasMany(DailyIssueComment::class)->latest();
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function isResponsible(User $user): bool
    {
        if ($this->relationLoaded('responsibles')) {
            return $this->responsibles->contains('id', $user->id);
        }

        return $this->responsibles()->where('user_id', $user->id)->exists();
    }

    /**
     * Only the original creator may delete an issue.
     */
    public function isDeletableBy(User $user): bool
    {
        return $this->created_by === $user->id;
    }

    /**
     * Any current Leader/Co Leader/Stack Lead of the team may edit, team-scoped not creator-scoped.
     */
    public function isEditableBy(User $user): bool
    {
        return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']);
    }

    /**
     * Only an assigned responsible person may mark a pending issue complete.
     */
    public function isCompletableBy(User $user): bool
    {
        return $this->status === 'pending' && $this->isResponsible($user);
    }

    /**
     * Any lead may reverse a completed issue back to pending, regardless of assignment.
     */
    public function isReversibleBy(User $user): bool
    {
        return $this->status === 'completed'
            && $user->team_id === $this->team_id
            && $user->hasAnyRole(['Leader', 'Co Leader', 'Stack Lead']);
    }

    /**
     * Only assigned responsible persons may post progress comments.
     */
    public function canCommentBy(User $user): bool
    {
        return $this->isResponsible($user);
    }
}
