<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['name', 'is_group', 'created_by'];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'chat_participants')
            ->withPivot('joined_at');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
