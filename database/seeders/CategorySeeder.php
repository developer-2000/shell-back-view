<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        // Получаем массив категорий из файла конфигурации
        $categories = config('site.categories.categories');
        // Получаем массив групп
        $groups = config('site.categories.groups');

        // Выбираем всех пользователей с ролью "cm"
        $cmUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'cm');
        })->get();

        if ($cmUsers->isEmpty()) {
            // выводит сообщение в консоль, когда сидер выполняется
            $this->command->info('No users with role "cm"');
            return;
        }

        foreach ($categories as $categoryName) {
            // Случайным образом выбираем одного из пользователей с ролью "cm"
            $manager = $cmUsers->random();

            // Создаем категорию
            Category::create([
                'name' => $categoryName,
                'manager_id' => $manager->id,
                'required' => true,
                'groups' => [$groups[0]]
            ]);
        }
    }
}
