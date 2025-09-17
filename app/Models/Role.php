<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    use HasFactory;

    protected $fillable = ['name'];

    public $timestamps = false;

    /**
     * Определяет связь многие ко многим с моделью User.
     */
    public function users() {
        // Указываем таблицу связи ('role_user'),
        // имя внешнего ключа для роли ('role_id')
        // имя внешнего ключа для пользователя ('user_id')
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }
}
