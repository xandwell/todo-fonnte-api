<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    protected $fillable = [
        'task_id',
        'status',
        'message_content',
        'retry_count',
        'error_message',
        'sent_at'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
