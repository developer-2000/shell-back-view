<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TypeSurface extends Model {
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
    ];

    // Связь с моделью Surface
    public function surfaces() {
        return $this->hasMany(Surface::class, 'type_surface', 'title');
    }
}
