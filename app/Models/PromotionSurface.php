<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionSurface extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'promotion_id',
        'surface_id',
        'designs',
    ];

    protected $casts = [
        'designs' => 'array',
    ];

    /**
     * Связь с моделью Promotion.
     */
    public function promotion() {
        return $this->belongsTo(Promotion::class, 'promotion_id'); // Указываем поле 'promotion_id'
    }

    /**
     * Связь с моделью Surface.
     */
    public function surface() {
        return $this->belongsTo(Surface::class, 'surface_id');
    }

    public function designs()
    {
        return $this->hasMany(PromotionSurfaceDesign::class, 'surface_id');
    }
}
