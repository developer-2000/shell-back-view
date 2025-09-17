<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Database\Seeder;
use App\Models\Promotion;
use Carbon\Carbon;

class PromotionSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Генерация случайных данных
        $faker = Factory::create();

        // Создание массива для вставки
        $promotions = [
            [
                'name' => 'Promotion 1',
                'status' => true,
                'period' => json_encode([
                    'from' => Carbon::create(2024, 9, 1, 0, 0, 0)->format('Y-m-d H:i:s'),
                    'to' => Carbon::create(2024, 10, 2, 23, 59, 59)->format('Y-m-d H:i:s'),
                ]),
                'description' => null,
                'surfaces' => json_encode([]),
                'url_images' => json_encode([]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Promotion 2',
                'status' => true,
                'period' => json_encode([
                    'from' => Carbon::create(2024, 9, 1, 0, 0, 0)->format('Y-m-d H:i:s'),
                    'to' => Carbon::create(2024, 10, 2, 23, 59, 59)->format('Y-m-d H:i:s'),
                ]),
                'description' => null,
                'surfaces' => json_encode([]),
                'url_images' => json_encode([]),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        // Вставка данных в базу
        Promotion::insert($promotions);
    }
}
