<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';

    protected $fillable = [
        'message',
        'object',
        'text',
    ];

    protected $casts = [
        'object' => 'array',
    ];
}
