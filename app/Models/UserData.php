<?php

namespace App\Models;

use App\Enums\CategoryGroup;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserData extends Model {
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'category_ids' => 'array'
    ];

    /**
     * Определяет связь "один к одному" с моделью User.
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array {
        return [
            'groups' => AsEnumCollection::of(CategoryGroup::class),
        ];
    }

    /**
     * Устанавливаем пустой массив по умолчанию
     */
    protected static function boot() {
        parent::boot();

        static::creating(function ($userData) {
            if (is_null($userData->category_ids)) {
                $userData->category_ids = [];
            }
        });
    }

    /**
     * Получает значение category_ids и преобразует его в массив чисел
     */
    public function getCategoryIdsAttribute($value): array {
        // Если null, возвращаем пустой массив
        $array = json_decode($value, true) ?? [];
        // Преобразуем строки в числа
        return array_map('intval', $array);
    }

    /**
     * Получает категории на основе category_ids и добавляет их в company_categories
     */
    public function getCompanyCategoriesAttribute() {
        return Category::whereIn('id', $this->category_ids)
            ->get(['id', 'name']);
    }
}
