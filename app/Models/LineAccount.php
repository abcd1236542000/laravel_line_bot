<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LineAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'line_user_id',
        'line_display_name',
        'enabled',
        'created_at',
        'updated_at',
    ];
}
