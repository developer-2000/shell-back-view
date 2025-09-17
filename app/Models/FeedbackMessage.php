<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackMessage extends Model {
    use HasFactory;

    protected $fillable = [
        'title',
        'messages',
        'from_user_id',
        'to_user_id'
    ];

    protected $casts = [
        'messages' => 'array',
    ];

    // Если у тебя есть связь с пользователями:
    public function fromUser() {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser() {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}

