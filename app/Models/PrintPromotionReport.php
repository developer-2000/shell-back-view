<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintPromotionReport extends Model {
    use HasFactory;

    protected $fillable = [
        'promotion_id',
        'percent',
        'description_cm',
        'surfaces',
    ];

    protected $casts = [
        'surfaces' => 'array',
    ];

    // Обратная связь с Promotion
    public function promotion() {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

}
