<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PromotionSurfaceDesign extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'design_id',
        'promotion_id',
        'surface_id',
        'chat_id',
        'design_category_id',
        'designer_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // Связь с промоакцией
    public function promotion() {
        return $this->belongsTo(Promotion::class, 'promotion_id');
    }

    // Установка значений по умолчанию
    protected static function boot() {
        parent::boot();

        static::creating(function ($model) {
            if (is_null($model->data)) {
                $model->data = [
                    'title' => '',
                    'sub_title' => '',
                    'text_italic' => '',
                    'ean_more' => '',
                    'promotional_offer' => 0,
                    'color' => 'No',
                    'supplier_discount' => '0%',
                    'plu_scan' => 'In the',
                    'status' => 'Created',
                    'description' => '',
                    'additional_description' => '',
                    'products' => [],
                    'files' => [],
                    'need_for_price' => [
                        'title' => 'Need for price change in store data',
                        'value' => false,
                    ],
                    'not_for_printing' => [
                        'title' => 'Not for printing',
                        'value' => false,
                    ],
                ];
            }
        });
    }

    // Связь с категорией
    public function category() {
        return $this->belongsTo(Category::class, 'design_category_id', 'id');
    }

    // Связь с дизайном
    public function design() {
        return $this->belongsTo(Design::class, 'design_id');
    }

    // Связь с поверхностью
    public function surface() {
        return $this->belongsTo(Surface::class, 'surface_id');
    }

    // Дизайнер этого дизайна
    public function designer() {
        return $this->belongsTo(User::class, 'designer_id');
    }
}
