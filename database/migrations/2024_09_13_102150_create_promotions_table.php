<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->tinyInteger('status')->default(0);
            $table->boolean('show_in_user_promotions')->default(false);
            $table->json('period');
            $table->text('description')->nullable();
            $table->json('surfaces');
            $table->tinyInteger('who_created_id')->nullable();
            $table->json('url_images')->nullable();
            $table->dateTime('notify_admin')->nullable();
            $table->dateTime('send_to_printer')->nullable();
            $table->dateTime('send_to_distributor')->nullable();
            $table->dateTime('complete_distributor_work')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('promotions');
    }
};
