<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogCompanyPlanner extends Model {
    use HasFactory;

    protected $fillable = [
        'user_id',
        'surface_id',
        'old_value',
        'new_value',
        ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function surface() {
        return $this->belongsTo(Surface::class);
    }

    // Accessor для получения объединённых данных
    public function getMergedDataAttribute(): array {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'surface_id' => $this->surface_id,
            'old_value' => $this->old_value,
            'new_value' => $this->new_value,
            'updated_at' => $this->updated_at,
            'user_name' => $this->user ? $this->user->name : null,
            'surface_name' => $this->surface ? $this->surface->name : null,
        ];
    }
}

