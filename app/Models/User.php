<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;
    protected $appends = ['is_online'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'employee_id',
        'avatar',
        'cover',
        'username',
        'name',
        'email',
        'official_email',
        'phone_code',
        'phone',
        'telegram',
        'github',
        'discord',
        'linkedin',
        'facebook',
        'whatsapp',
        'stack_id',
        'team_id',
        'community_id',
        'reporting_to',
        'designation',
        'dob',
        'joining_date',
        'probation_end_date',
        'employment_status',
        'weekend',
        'address',
        'otp',
        'password',
        'status',
        'is_admin',
        'is_request',
        'added_by',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function addBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public function stack()
    {
        return $this->belongsTo(Stack::class);
    }

    public function reportingTo()
    {
        return $this->belongsTo(User::class, 'reporting_to');
    }

    public function subordinates()
    {
        return $this->hasMany(User::class, 'reporting_to');
    }


    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification());
    }

    public function getIsOnlineAttribute()
    {
        return DB::table('sessions')
            ->where('user_id', $this->id)
            ->where('last_activity', '>=', now()->subMinutes(5)->timestamp) // Last 5 minutes activity
            ->exists();
    }

    public function sentChats()
    {
        return $this->hasMany(Chat::class, 'sender_id');
    }

    public function receivedChats()
    {
        return $this->hasMany(Chat::class, 'receiver_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class, 'sender_id',$this->id)
            ->orWhere('receiver_id', $this->id);
    }

}
