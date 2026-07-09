<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'created_by',
        'team_id',
        'details',
        'due_date',
        'source',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function isPersonal(): bool
    {
        return $this->source === 'personal';
    }

    public function isAssigned(): bool
    {
        return $this->source === 'assigned';
    }

    /**
     * Only the owner may check their own reminder as completed.
     */
    public function isCompletableBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Personal reminders are editable by their owner only; assigned reminders
     * are editable by any current Leader/Co Leader of the team.
     */
    public function isEditableBy(User $user): bool
    {
        if ($this->isAssigned()) {
            return $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader']);
        }

        return $this->user_id === $user->id;
    }

    /**
     * Only an assigned reminder may be cancelled by a current Leader/Co Leader
     * of the team, before the owner completes it.
     */
    public function isCancelableBy(User $user): bool
    {
        return $this->isAssigned() && $user->team_id === $this->team_id && $user->hasAnyRole(['Leader', 'Co Leader']);
    }

    public function daysLeftLabel(): string
    {
        $days = today()->diffInDays($this->due_date, false);
        $dueDate = $this->due_date->format('d F Y');

        if ($days === 0) {
            return "Due today - You must {$this->details}";
        }

        if ($days < 0) {
            return "Was due on {$dueDate} - You must {$this->details}; " . abs($days) . ' day' . (abs($days) === 1 ? '' : 's') . ' overdue';
        }

        return "On {$dueDate} - You must {$this->details}; you have only {$days} day" . ($days === 1 ? '' : 's') . ' left';
    }
}
