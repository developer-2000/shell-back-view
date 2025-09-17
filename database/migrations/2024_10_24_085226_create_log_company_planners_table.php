<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('log_company_planners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('surface_id')->constrained('surfaces')->onDelete('cascade');
            $table->integer('old_value');
            $table->integer('new_value');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('log_company_planners');
    }

};
