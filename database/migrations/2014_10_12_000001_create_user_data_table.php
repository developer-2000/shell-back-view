<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('user_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('company_name')->nullable();
            $table->string('surname')->nullable();
            $table->string('phone')->nullable();
            $table->string('name_invoice_recipient')->nullable();
            $table->string('company_number')->nullable();
            $table->string('email_invoice_recipient')->nullable();
            $table->string('reference_number')->nullable();
            $table->string('c_o')->nullable();
            $table->string('post_address')->nullable();
            $table->string('postcode')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('municipality_number')->nullable();
            $table->string('kommune')->nullable();
            $table->string('country')->nullable();
            $table->string('number_country')->nullable();
            $table->string('group')->nullable();
            $table->json('category_ids')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('user_data');
    }
};
