<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration {
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('ean');
            $table->unsignedInteger('vendor_code');
            $table->string('name');
            $table->string('category');
            $table->string('sub_category');
            $table->string('provider_name');
            $table->string('manufacturer');
            $table->decimal('price_per_item', 12, 2);
            $table->decimal('main_last_price', 12, 2);
            $table->decimal('latest_price', 12, 2);
            $table->boolean('status');
            $table->unsignedInteger('tax');
            $table->unsignedInteger('container_deposit');
            $table->string('item_plan_bu_grp');
            $table->string('locally_owned');
            $table->string('selling_type');
            $table->string('provider_item_ean');
            $table->string('provider_item_name');
            $table->unsignedInteger('provider_item_pack_qty');
            $table->string('provider_item_vendor_code');
            $table->json('url_images')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down() {
        Schema::dropIfExists('products');
    }
}
