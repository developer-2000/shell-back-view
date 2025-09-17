<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProductSeeder extends Seeder {

    public function run() {
        // Генерация случайных данных
        $faker = Factory::create();
        // Получаем массивы категорий и подкатегорий из конфигурационного файла
        $categories = config('site.products.categories');
        $subCategories = config('site.products.sub_categories');

        // Пример генерации 10 записей
        for ($i = 0; $i < 3; $i++) {
            DB::table('products')->insert([
                'ean' => $faker->numberBetween(10000000, 99999999),
                'vendor_code' => $faker->numberBetween(100, 999),
                'name' => $faker->word,
                'category' => $categories[array_rand($categories)],
                'sub_category' => $subCategories[array_rand($subCategories)],
                'provider_name' => $faker->company,
                'manufacturer' => $faker->company,
                'price_per_item' => $faker->randomFloat(2, 1, 100),
                'main_last_price' => $faker->randomFloat(2, 1, 100),
                'latest_price' => $faker->randomFloat(2, 1, 100),
                'status' => true,
                'tax' => $faker->numberBetween(1, 10),
                'container_deposit' => $faker->numberBetween(1, 10),
                'item_plan_bu_grp' => 'a_' . $i,
                'locally_owned' => 'b_' . $i,
                'selling_type' => 'c_' . $i,
                'provider_item_ean' => 'd_' . $i,
                'provider_item_name' => 'e_' . $i,
                'provider_item_pack_qty' => $faker->numberBetween(1, 10),
                'provider_item_vendor_code' => 'f_' . $i,
                'url_images' => [],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
