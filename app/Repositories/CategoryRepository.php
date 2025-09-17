<?php

namespace App\Repositories;

use App\Models\Category;
use Exception;

class CategoryRepository {

    /**
     * Создание и обновление категории
     * @param array $validatedData
     * @return array
     */
    public function saveCategory(array $validatedData): array {
        $message = '';

        // обновляем существующую запись
        if (isset($validatedData['id']) && $validatedData['id']) {
            if ($category = Category::find($validatedData['id'])) {
                $category->update($validatedData);
                $message = 'Category updated successfully!';
            }
            else {
                return ['success' => false, 'message' => 'Category not found!', 'status_code' => 404];
            }
        }
        // Создаем новую запись
        else {
            $category = Category::create($validatedData);
            $message = 'Category created successfully!';
        }

        return ['success' => true, 'message' => $message, 'status_code' => 200];
    }

    /**
     * Удаляет категорию по её ID.
     *
     * @param int $categoryId
     * @return array
     */
    public function deleteCategory(int $categoryId): array
    {
        try {
            $category = Category::find($categoryId);

            if (!$category) {
                return ['success' => false, 'message' => 'Category not found', 'status_code' => 404];
            }

            // Мягкое удаление
            $category->delete();
            return ['success' => true, 'message' => 'Category deleted successfully', 'status_code' => 200];
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Получает все категории с полем 'name'.
     *
     * @return array
     */
    public function getAllCategoryNames(): array {
        return Category::pluck('name')->toArray();
    }
}
