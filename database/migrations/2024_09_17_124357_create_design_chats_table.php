<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create('design_chats', function (Blueprint $table) {
            $table->id();
            $table->json('messages');
            $table->json('socket_users_ids');
            $table->json('job_timer_user_ids');
            $table->json('send_email_user_ids');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists('design_chats');
    }
};
