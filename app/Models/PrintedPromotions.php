<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintedPromotions extends Model {
    use HasFactory;

    protected $table = 'printed_promotions';

    protected $fillable = [
        'promotion_id',
        'printer_id',
        'printer_tracker_number',
        'sent_surfaces',
        'description',
    ];

    protected $casts = [
        'sent_surfaces' => 'array',
    ];

    public function promotion() {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    public function printer() {
        return $this->belongsTo(User::class, 'printer_id', 'id');
    }

}
