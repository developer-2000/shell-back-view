<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('surfaces', function (Blueprint $table) {
            $table->id();
            $table->integer('vendor_code');
            $table->string('name');

            // Поле для связи с title таблицы type_surfaces
            $table->string('type_surface')->nullable();
            $table->foreign('type_surface')->references('title')->on('type_surfaces');

            // Поле для связи с title таблицы size_surfaces
            $table->string('size_surface')->nullable();
            $table->foreign('size_surface')->references('title')->on('size_surfaces');

            $table->text('description')->nullable();
            $table->string('status');
            $table->boolean('divided_bool');
            $table->json('url_images')->nullable();

            $table->unsignedBigInteger('printer_id')->nullable();
            $table->foreign('printer_id')->references('id')->on('users');
            $table->decimal('price', 8, 2)->default(0)->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('surfaces');
    }
};
