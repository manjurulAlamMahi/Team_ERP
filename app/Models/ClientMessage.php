<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'client_message_type_id',
        'submitted_by',
        'reviewed_by',
        'client_id',
        'client_name',
        'profile_name',
        'last_message_type',
        'their_message',
        'status',
        'rejection_reason',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function type()
    {
        return $this->belongsTo(ClientMessageType::class, 'client_message_type_id')->withTrashed();
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function submitter()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function attachments()
    {
        return $this->hasMany(ClientMessageAttachment::class);
    }

    public function lastMessageAttachments()
    {
        return $this->hasMany(ClientMessageAttachment::class)->where('type', 'last_message');
    }

    public function fileAttachments()
    {
        return $this->hasMany(ClientMessageAttachment::class)->where('type', 'attachment');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Only the original submitter, only while pending, may edit/delete.
     */
    public function isEditableBy(User $user): bool
    {
        return $this->isPending() && $this->submitted_by === $user->id;
    }

    /**
     * Leader/Co Leader of the same team may review (not stack-scoped).
     */
    public function isReviewableBy(User $user): bool
    {
        return $this->isPending()
            && $user->team_id === $this->team_id
            && $user->hasAnyRole(['Leader', 'Co Leader']);
    }

    public function scopeForTeam($query, int $teamId)
    {
        return $query->where('team_id', $teamId);
    }
}
