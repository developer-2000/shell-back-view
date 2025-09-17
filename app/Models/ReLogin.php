<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReLogin extends Model {
    use HasFactory;

    protected $fillable = [
        'from_user_id',
        'to_user_id',
    ];

    /**
     * Определяет связь с моделью User (откуда).
     */
    public function fromUser() {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    /**
     * Определяет связь с моделью User (куда).
     */
    public function toUser() {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}

