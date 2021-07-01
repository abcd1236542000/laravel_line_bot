<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineHookRecord extends Model
{
    use HasFactory;
    const TYPE_WEBHOOK_EVENT = 'webhook_event';
    const TYPE_NOTIFY_AUTH   = 'notify_auth';

    protected $fillable = [
        'content',
        'type',
        'ip_address',
        'user_agent',
        'created_at',
        'updated_at',
    ];
}
