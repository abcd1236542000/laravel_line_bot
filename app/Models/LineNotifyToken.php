<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineNotifyToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'token',
        'target_type',
        'target',
        'enabled',
        'created_at',
        'updated_at',
    ];
}