<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Promotion extends Model {
    use HasFactory, SoftDeletes;

    const STATUS_DRAFT = 0;
    const STATUS_DESIGNER_WORKING = 1;
    const STATUS_DESIGNER_COMPLETED = 2;
    const STATUS_PRINTED_BY_THE_PRINTER = 3;
    const STATUS_SENT_BY_THE_DISTRIBUTOR = 4;

    protected $fillable = [
        'name',
        'status',
        'show_in_user_promotions',
        'period',
        'description',
        'surfaces',
        'notify_admin',
        'send_to_printer',
        'send_to_distributor',
        'complete_distributor_work',
        'who_created_id',
        'url_images',
    ];

    protected $casts = [
        'period' => 'array',
        'surfaces' => 'array',
        'url_images' => 'array',
        'status' => 'integer',
        'show_in_user_promotions' => 'boolean',
        'notify_admin' => 'date',
        'send_to_printer' => 'date',
        'send_to_distributor' => 'date',
        'complete_distributor_work' => 'date',
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

    // Обновление статуса на 0
    public function setDraftStatus(): void {
        $this->status = self::STATUS_DRAFT;
        $this->save();
    }
    // Обновление статуса на 1
    public function setDesignerWorkingStatus(): void {
        $this->status = self::STATUS_DESIGNER_WORKING;
        $this->save();
    }
    // Обновление статуса на 2
    public function setDesignerCompletedStatus(): void {
        $this->status = self::STATUS_DESIGNER_COMPLETED;
        $this->save();
    }
    // Обновление статуса на 3
    public function setPrintedByThePrinterStatus(): void {
        $this->status = self::STATUS_PRINTED_BY_THE_PRINTER;
        $this->save();
    }
    // Обновление статуса на 4
    public function setSentByTheDistributorStatus(): void {
        $this->status = self::STATUS_SENT_BY_THE_DISTRIBUTOR;
        $this->save();
    }

    /**
     * Кто создал Promotion
     *
     * @return BelongsTo
     */
    public function whoCreated(): BelongsTo {
        return $this->belongsTo(User::class, 'who_created_id');
    }

    // Определите связь с PromotionSurface
    public function promotionSurfaces() {
        return $this->hasMany(PromotionSurface::class);
    }

    // Accessor для получения поверхностей
    public function getSurfacesAttribute(): array {
        $validated = ['promotion_id' => $this->id]; // ID текущей акции
        $promotionSurfaces = $this->getAllPromotionSurfaces($validated);

        return $promotionSurfaces ?? [];
    }

    // Поверхности с их дизайнами для поля surfaces
    public function getAllPromotionSurfaces(array $validated): array {
        // 1. Выбираем все поверхности акции
        $promotionSurfaces = PromotionSurface::where('promotion_id', $validated['promotion_id'])
            ->with('surface')
            ->get();

        // Если нет записей, возвращаем пустой массив
        if ($promotionSurfaces->isEmpty()) {
            return [];
        }

        // Итоговый массив для ответов
        $result = $promotionSurfaces->map(function ($promotionSurface) use ($validated) {
            // Получаем поверхность
            $surface = $promotionSurface->surface;

            // Если поверхность не найдена, пропускаем эту запись
            if (!$surface) {
                return null;
            }

            // 3. Выбираем все дизайны для этой поверхности и акции
            $promotionSurfaceDesigns = PromotionSurfaceDesign::where('promotion_id', $promotionSurface->promotion_id)
                ->where('surface_id', $promotionSurface->surface_id)
                ->get();

            // Формируем данные для этой записи
            return [
                'surface' => [
                    'id' => $surface->id,
                    'name' => $surface->name,
                    'printer_id' => $surface->printer_id
                ],
                'designs' => $promotionSurfaceDesigns->map(function ($promotionSurface) {
                    $designCollect = Design::where("id", $promotionSurface->design_id)->first();

                    return [
                        'id' => $promotionSurface->id,
                        'name' => $designCollect ? $designCollect->name : "No name",
                        'data' => $promotionSurface->data,
                        'category' => $promotionSurface->design_category_id,
                    ];
                })->toArray()
            ];

        })->filter()->values()->toArray();

        return $result;
    }

    // Добавляем связь с PrintPromotionReport
    public function printPromotionReport() {
        return $this->hasOne(PrintPromotionReport::class, 'promotion_id');
    }

    // Добавляем связь с PrintedPromotions
    public function printedPromotions() {
        return $this->hasOne(PrintedPromotions::class, 'promotion_id');
    }
}

