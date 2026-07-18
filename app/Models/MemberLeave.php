<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MemberLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'user_id',
        'created_by',
        'start_date',
        'end_date',
        'status',
        'reason',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
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

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    /**
     * Only a current Leader/Co Leader of the team may manage leave records; members never see these.
     */
    public function isManageableBy(User $user): bool
    {
        return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader']);
    }

    public function dateRangeLabel(): string
    {
        if ($this->start_date->isSameDay($this->end_date)) {
            return $this->start_date->format('d M Y');
        }

        return $this->start_date->format('d M Y') . ' - ' . $this->end_date->format('d M Y');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'absent' => 'Absent',
            'leave' => 'Leave',
            'home_office' => 'Home Office',
            default => $this->status,
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'absent' => 'bg-danger',
            'leave' => 'bg-warning',
            'home_office' => 'bg-info',
            default => 'bg-secondary',
        };
    }
}
