<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id', 'user_id', 'created_by',
        'task_date', 'client_id', 'client_name', 'profile_name', 'plan_details',
        'expected_complete_date', 'source', 'status',
        'completed_at', 'remarks', 'remarks_by', 'remarks_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'task_date'              => 'date',
            'expected_complete_date' => 'date',
            'completed_at'           => 'datetime',
            'remarks_updated_at'     => 'datetime',
        ];
    }

    public function team()       { return $this->belongsTo(Team::class); }
    public function client()     { return $this->belongsTo(Client::class); }
    public function user()       { return $this->belongsTo(User::class, 'user_id'); }
    public function creator()    { return $this->belongsTo(User::class, 'created_by'); }
    public function remarksByUser() { return $this->belongsTo(User::class, 'remarks_by'); }

    public function scopeForTeam($query, int $teamId)    { return $query->where('team_id', $teamId); }
    public function scopeForUser($query, int $userId)    { return $query->where('user_id', $userId); }
    public function scopeForDate($query, $date)          { return $query->whereDate('task_date', $date); }
    public function scopePending($query)                 { return $query->where('status', 'pending'); }
    public function scopeCompleted($query)               { return $query->where('status', 'completed'); }

    public function getTaskByLabelAttribute(): string
    {
        return match ($this->source) {
            'leader'     => 'Leader',
            'co_leader'  => 'Co Leader',
            'stack_lead' => 'Stack Leader',
            default      => 'My Self',
        };
    }

    public function getFormattedDateAttribute(): string
    {
        if ($this->task_date->isToday()) return 'Today';
        if ($this->task_date->isYesterday()) return 'Yesterday';
        return $this->task_date->format('d F Y');
    }

    public function canBeEditedBy(User $actor): bool
    {
        if ($actor->team_id !== $this->team_id) return false;
        if ($actor->hasAnyRole(['Leader', 'Co Leader'])) return true;
        if ($actor->hasRole('Stack Lead')) return $actor->stack_id === $this->user->stack_id;
        return false;
    }

    public function canBeDeletedBy(User $actor): bool
    {
        return $actor->team_id === $this->team_id && $actor->hasAnyRole(['Leader', 'Co Leader']);
    }

    public function canRemarksBeEditedBy(User $actor): bool
    {
        return $this->canBeEditedBy($actor);
    }
}
