<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Group extends Model
{

    protected $table = 'group_messages';
    protected $fillable = [
        'user_id',
        'message'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationship to user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
