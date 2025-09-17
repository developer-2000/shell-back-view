<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyPlanner extends Model {

    protected $fillable = ['user_id', 'surfaces'];

    protected $casts = [
        'surfaces' => 'array'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

