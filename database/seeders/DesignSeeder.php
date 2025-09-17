<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Design;
use App\Models\Category;

class DesignSeeder extends Seeder {
    public function run(): void {
        $categoryIds = Category::pluck('id')->toArray();

        // Генерируем 3 дизайна
        Design::create([
            'name' => 'First design',
            'category_id' => $categoryIds[array_rand($categoryIds)]
        ]);

        Design::create([
            'name' => 'Second design',
            'category_id' => $categoryIds[array_rand($categoryIds)]
        ]);

        Design::create([
            'name' => 'Third design',
            'category_id' => $categoryIds[array_rand($categoryIds)]
        ]);
    }
}
