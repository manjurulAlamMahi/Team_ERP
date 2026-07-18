<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'created_by',
        'title',
        'description',
        'priority',
        'ends_at',
    ];

    protected function casts(): array
    {
        return [
            'ends_at' => 'date',
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

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }

    public function scopeActive($query)
    {
        return $query->where('ends_at', '>=', today());
    }

    public function isActive(): bool
    {
        return $this->ends_at->gte(today());
    }

    /**
     * Any current Leader/Co Leader of the team may edit or delete, team-scoped not creator-scoped.
     */
    public function isEditableBy(User $user): bool
    {
        return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader']);
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'urgent' => 'bg-danger',
            'warning' => 'bg-warning',
            default => 'bg-info',
        };
    }

    public function priorityAlertClass(): string
    {
        return match ($this->priority) {
            'urgent' => 'alert-danger',
            'warning' => 'alert-warning',
            default => 'alert-info',
        };
    }
}
