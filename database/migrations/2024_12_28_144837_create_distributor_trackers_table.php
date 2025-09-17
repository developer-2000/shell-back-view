<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistributorTrackersTable extends Migration {
    public function up() {
        Schema::create('distributor_trackers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->unsignedBigInteger('company_id');
            $table->string('tracker_number');
            $table->json('sent_surfaces');
            $table->string('description')->nullable();
            $table->timestamps();

            // Добавляем внешние ключи
            $table->foreign('promotion_id')->references('id')->on('promotions')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down() {
        Schema::dropIfExists('distributor_trackers');
    }
}
