<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model {
    use HasFactory;

    protected $fillable = ['text', 'json'];

    protected $casts = [
        'json' => 'array', // Кастим поле json как массив
    ];
}

