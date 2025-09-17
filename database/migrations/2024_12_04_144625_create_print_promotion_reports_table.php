<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('print_promotion_reports', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('promotion_id');
             $table->foreign('promotion_id')
                 ->references('id')
                 ->on('promotions')
                 ->onDelete('cascade');

            $table->integer('percent')->default(0);
            $table->text('description_cm')->nullable();
            $table->json('surfaces');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('print_promotion_reports');
    }
};
