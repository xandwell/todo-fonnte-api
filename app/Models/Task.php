<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'title', 'reminder', 'due_date', 'user_id', 'is_completed'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_completed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
