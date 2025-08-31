<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',        // ADD THIS
        'is_online',     // ADD THIS
        'last_seen_at',  // ADD THIS
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
            'is_online' => 'boolean',      // ADD THIS
            'last_seen_at' => 'datetime',  // ADD THIS
        ];
    }

    // ADD THESE RELATIONSHIPS
    /**
     * Get all chats that this user participates in
     */
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_participants');
    }

    /**
     * Get all messages sent by this user
     */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Get user settings
     */
    public function settings()
    {
        return $this->hasOne(UserSetting::class);
    }
}
