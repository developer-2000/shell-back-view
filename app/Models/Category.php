<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CategoryGroup;
use Illuminate\Database\Eloquent\Casts\AsEnumCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model {
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'manager_id',
        'groups',
        'required',
    ];

    /**
     * Get the manager that owns the category.
     */
    public function manager(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array {
        return [
            'groups' => AsEnumCollection::of(CategoryGroup::class),
            'required' => 'boolean',
        ];
    }
}
