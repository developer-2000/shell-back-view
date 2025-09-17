<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DesignChat extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'messages',
        'socket_users_ids',
        'job_timer_user_ids',
        'send_email_user_ids',
    ];

    protected $casts = [
        'messages' => 'array',
        'socket_users_ids' => 'array',
        'job_timer_user_ids' => 'array',
        'send_email_user_ids' => 'array',
    ];

    public function promotionSurfaceDesign() {
        return $this->hasOne(PromotionSurfaceDesign::class, 'chat_id');
    }

    // при создании - socket_users_ids будет установлен как [], если он null
    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->socket_users_ids = $model->socket_users_ids ?? [];
            $model->job_timer_user_ids = $model->job_timer_user_ids ?? [];
            $model->send_email_user_ids = $model->send_email_user_ids ?? [];
        });

        static::updating(function ($model) {
            $model->socket_users_ids = $model->socket_users_ids ?? [];
            $model->job_timer_user_ids = $model->job_timer_user_ids ?? [];
            $model->send_email_user_ids = $model->send_email_user_ids ?? [];
        });
    }

}
