<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('size_surfaces', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique(); // Добавляем уникальный индекс
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('size_surfaces');
    }
};
