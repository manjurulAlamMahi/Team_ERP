<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyIssueComment extends Model
{
    protected $fillable = [
        'daily_issue_id',
        'user_id',
        'comment',
        'type',
    ];

    public function issue()
    {
        return $this->belongsTo(DailyIssue::class, 'daily_issue_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
