<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ean',
        'vendor_code',
        'name',
        'category',
        'sub_category',
        'provider_name',
        'manufacturer',
        'price_per_item',
        'main_last_price',
        'latest_price',
        'status',
        'tax',
        'container_deposit',
        'item_plan_bu_grp',
        'locally_owned',
        'selling_type',
        'provider_item_ean',
        'provider_item_name',
        'provider_item_pack_qty',
        'provider_item_vendor_code',
        'url_images',
    ];

    protected $casts = [
        'status' => 'boolean',
        'price_per_item' => 'decimal:2',
        'main_last_price' => 'decimal:2',
        'latest_price' => 'decimal:2',
        'ean' => 'integer',
        'vendor_code' => 'integer',
        'tax' => 'integer',
        'container_deposit' => 'integer',
        'provider_item_pack_qty' => 'integer',
        'url_images' => 'array',
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

    // Аксессор для поля name
    public function getNameAttribute($value) {
        return ucfirst($value); // Преобразуем первую букву к заглавной
    }

    // Аксессор для поля category
    public function getCategoryAttribute($value) {
        return ucfirst($value);
    }

    // Аксессор для поля sub_category
    public function getSubCategoryAttribute($value) {
        return ucfirst($value);
    }


}
