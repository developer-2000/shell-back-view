<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DistributorTracker extends Model {
    use HasFactory;

    // Указываем таблицу
    protected $table = 'distributor_trackers';

    protected $fillable = [
        'promotion_id',
        'company_id',
        'tracker_number',
        'sent_surfaces',
        'description',
    ];

    protected $casts = [
        'sent_surfaces' => 'array',
    ];

}
