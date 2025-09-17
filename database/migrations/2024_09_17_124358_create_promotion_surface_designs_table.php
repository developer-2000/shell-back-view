<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('promotion_surface_designs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('design_id');
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('surface_id');
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('design_category_id');
            $table->unsignedBigInteger('designer_id')->nullable();

            // Foreign keys with cascade on delete
            $table->foreign('design_category_id')->references('id')->on('categories')->onDelete('cascade');
            $table->foreign('design_id')->references('id')->on('designs')->onDelete('cascade');
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            $table->foreign('surface_id')->references('id')->on('surfaces')->onDelete('cascade');
            $table->foreign('chat_id')->references('id')->on('design_chats')->onDelete('cascade');

            $table->json('data');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('promotion_surface_designs');
    }
};

