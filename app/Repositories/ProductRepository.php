<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Exception;


class ProductRepository extends BaseRepository {

    public function getProducts(array $params): array {

        $query = $this->baseQuery();

        // Выбираем необходимые поля
        $this->selectFields($query);
        // Выбираем все поля из таблицы surfaces и связанные поля из type_surfaces и size_surfaces

        // 1 Применяем поиск в указанных полях
        $query = $this->applySearchFilter($query, $params['field'], $params['input_value']);

        // 2 Применяем фильтрацию по категории
        if (!empty($params['only_category'])) {
            $query->where('category', '=', $params['only_category']);
        }

        // 3 Применяем фильтрацию по подкатегории
        if (!empty($params['only_subcategory'])) {
            $query->where('sub_category', '=', $params['only_subcategory']);
        }

        // 4 Применяем сортировку по sort_by и sort_count
        $sortableFields = [
            'ean' => 'ean',
            'vendor_code' => 'vendor_code',
            'name' => 'name',
            'category' => 'category',
            'sub_category' => 'sub_category',
            'provider_name' => 'provider_name',
            'manufacturer' => 'manufacturer',
            'price_per_item' => 'price_per_item',
            'status' => 'status',
        ];
        // Сортировка по полю
        $this->applySorting($query, $params, $sortableFields, 'ean');

        // Получаем общее количество записей
        $total = $query->count();
        // Получаем отсортированные и постраничные данные
        $products = $query->skip($params['start_index'])->take($params['count_show'])->get();
        // Конфиг данные для продукта
        $categories = config("site.products.categories");
        $sub_categories = config("site.products.sub_categories");

        return [
            'products' => $products,
            'total' => $total,
            'categories' => $categories,
            'sub_categories' => $sub_categories,
        ];
    }

    /**
     * Создает новый продукт
     */
    public function createProduct(array $validated): array {
        try {
            // Подготавливаем данные для обновления
            $productData = $this->prepareProductData($validated);

            // Создаем новый surface в базе
            $product = Product::create($productData);

            // Проверка на успешное создание
            if ($product) {
                return ['success' => true, 'message' => 'Product saved successfully!', 'status_code' => 201];
            }
            else {
                return ['success' => false, 'message' => 'Failed to create product.', 'status_code' => 500];
            }
        }
        catch (Exception $e) {
            return ['success' => false, 'message' => 'An error occurred: ' . $e->getMessage(), 'status_code' => 500];
        }
    }

    /**
     * Обновление продукта.
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Ответ с результатом обновления.
     */
    public function updateProduct(array $validated, Product $product): array {
        try {
            // Подготавливаем данные для обновления
            $productData = $this->prepareProductData($validated);

            // Обновляем продукт в базе
            $updateSuccess = $product->update($productData);

            // Проверка на успешное обновление
            if ($updateSuccess) {
                return [
                    'success' => true,
                    'message' => 'Product updated successfully!',
                    'status_code' => 200
                ];
            }
            else {
                return [
                    'success' => false,
                    'message' => 'Failed to update product.',
                    'status_code' => 500
                ];
            }
        }
        catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Мягкое удаление по ID.
     * @param $currentUser
     * @param $product_id
     * @return array
     */
    public function deleteProduct($currentUser, Product $product): array {
        // Проверяем роль текущего пользователя
        if (!$currentUser->hasRole('admin')) {
            return ['success' => false, 'message' => "You do not have permission to delete product.", 'status_code' => 403];
        }

        // Мягкое удаление
        $product->delete();

        return ['success' => true, 'message' => "Product successfully deleted.", 'status_code' => 200];
    }

    /**
     * Загрузить все продукты
     * @return array
     */
    public function getAllProducts(): array {
        try {
            $products = $this->baseQuery()->get();

            return [
                'success' => true,
                'message' => 'Products loaded successfully.',
                'status_code' => 200,
                'data' => $products->toArray()
            ];
        }
        catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to load products: ' . $e->getMessage(),
                'status_code' => 500,
                'data' => []
            ];
        }
    }

    /**
     * Подготавливает данные продукта
     *
     * @param array $validated Данные, прошедшие валидацию.
     * @return array Данные продукта.
     */
    private function prepareProductData(array $validated): array {
        $data = $validated;  // Сохраняем все данные из валидации
        $data['status'] = true;  // Добавляем поле status

        return $data;
    }

    /**
     * Применяет фильтрацию по указанному полю.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query Запрос к базе данных.
     * @param string $field Поле для фильтрации.
     * @param string $inputValue Значение для фильтрации.
     * @return \Illuminate\Database\Eloquent\Builder Обновленный запрос.
     */
    private function applySearchFilter(Builder $query, $field, $inputValue): Builder {
        if (!empty($field) && !empty($inputValue)) {
            // Разделяем поисковую строку на отдельные термины
            $searchTerms = explode(' ', strtolower($inputValue));

            // Фильтрация по каждому поисковому термину
            $query->where(function ($query) use ($field, $searchTerms) {
                foreach ($searchTerms as $term) {
                    $term = trim($term);
                    if (!empty($term)) {
                        switch ($field) {
                            case 'ean':
                                $query->whereRaw('LOWER(ean) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'vendor_code':
                                $query->whereRaw('LOWER(vendor_code) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'name':
                                $query->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'provider_name':
                                $query->whereRaw('LOWER(provider_name) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'manufacturer':
                                $query->whereRaw('LOWER(manufacturer) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'price_per_item':
                                $query->whereRaw('LOWER(price_per_item) LIKE ?', ["%{$term}%"]);
                                break;

                            case 'all':
                                // Если поле 'all', ищем по всем полям
                                $query->where(function($query) use ($term) {
                                    $query->whereRaw('LOWER(ean) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(vendor_code) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(provider_name) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(manufacturer) LIKE ?', ["%{$term}%"])
                                        ->orWhereRaw('LOWER(price_per_item) LIKE ?', ["%{$term}%"]);
                                });
                                break;

                            default:
                                break;
                        }
                    }
                }
            });
        }

        return $query;
    }

    /**
     * Формирует базовый запрос для пользователей.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    private function baseQuery(): Builder {
        $query = Product::query();

        return $query;
    }

    /**
     * Выбирает необходимые поля для запроса.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return void
     */
    private function selectFields(Builder $query): void {
        $query->select('*');
    }
}
