<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model {
    use HasFactory;

    protected $fillable = [
        'distributor_id',
        'admin_id',
        'percent_promotion_report',
    ];

    public function distributor() {
        return $this->belongsTo(User::class, 'distributor_id');
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
