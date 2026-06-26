<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TodayPlanTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'created_by',
        'reviewed_by',
        'plan_date',
        'client_name',
        'profile_name',
        'details',
        'source',
        'status',
        'review_comment',
        'reviewed_at',
        'is_completed',
        'completed_at',
        'leader_verified',
        'completion_comment',
    ];

    protected function casts(): array
    {
        return [
            'plan_date' => 'date',
            'reviewed_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_completed' => 'boolean',
            'leader_verified' => 'boolean',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('plan_date', $date);
    }

    public function isPlanned(): bool
    {
        return $this->source === 'planned';
    }

    public function isLeaderAssigned(): bool
    {
        return $this->source === 'leader_assigned';
    }

    public function isPersonal(): bool
    {
        return $this->source === 'personal';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Submitter may edit/delete their own pending planned item; any current
     * Leader of the team may edit/delete a leader-assigned item; owner may
     * edit/delete their own personal task at any time.
     */
    public function isEditableBy(User $user): bool
    {
        if ($this->isLeaderAssigned()) {
            return $user->team_id === $this->team_id && $user->hasRole('Leader');
        }

        if ($this->isPersonal()) {
            return $this->user_id === $user->id;
        }

        return $this->isPending() && $this->user_id === $user->id;
    }

    /**
     * Only Leader (not Co Leader, not Stack Lead) reviews planned items.
     */
    public function isReviewableBy(User $user): bool
    {
        return $this->isPlanned()
            && $this->isPending()
            && $user->team_id === $this->team_id
            && $user->hasRole('Leader');
    }

    /**
     * Only Leader of the same team may verify/reopen a completed task.
     */
    public function isVerifiableBy(User $user): bool
    {
        return $this->is_completed
            && $user->team_id === $this->team_id
            && $user->hasRole('Leader');
    }

    /**
     * Owner toggles their own task's completion checkbox; only approved
     * items are eligible.
     */
    public function isCompletableBy(User $user): bool
    {
        return $this->isApproved() && $this->user_id === $user->id;
    }
}
