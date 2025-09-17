<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Surface extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_code',
        'name',
        'type_surface',
        'size_surface',
        'price',
        'description',
        'status',
        'divided_bool',
        'url_images',
        'printer_id',
    ];

    protected $casts = [
        'status' => 'array',
        'url_images' => 'array',
        'divided_bool' => 'boolean'
    ];

    // при создании - будет установлен как [], если он null
    protected static function boot() {
        parent::boot();
        static::creating(function ($model) {
            $model->url_images = $model->url_images ?? [];
        });

        static::updating(function ($model) {
            $model->url_images = $model->url_images ?? [];
        });
    }

    public function designs() {
        return $this->hasMany(PromotionSurfaceDesign::class, 'surface_id');
    }

    // Связь с User по полю printer_id
    public function printer() {
        return $this->hasOne(User::class, 'id', 'printer_id');
    }

}
