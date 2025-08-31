<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = ['name', 'is_group', 'created_by'];

    protected $casts = [
        'is_group' => 'boolean',
    ];

    public function participants()
    {
        return $this->belongsToMany(User::class, 'chat_participants');
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
