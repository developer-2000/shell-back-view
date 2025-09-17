<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Products\ProductPaginationRequest;
use App\Http\Requests\Products\ProductSaveRequest;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseController {

    protected ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository) {
        $this->productRepository = $productRepository;
    }

    /**
     * Продукты в пагинации, с фильтрами и сортировкой для таблицы на странице Products
     * @param ProductPaginationRequest $request
     * @return JsonResponse
     */
    public function index(ProductPaginationRequest $request): JsonResponse {
        $validatedData = $request->validated();

        $params = [
            'count_show' => $validatedData['count_show'],
            'field' => $validatedData['obj_search']['field'] ?? '',
            'input_value' => $validatedData['obj_search']['input_value'] ?? '',
            'only_category' => $validatedData['obj_search']['only_category'] ?? '',
            'only_subcategory' => $validatedData['obj_search']['only_subcategory'] ?? '',
            'start_index' => $validatedData['start_index'],
            'sort_by' => $validatedData['sort_by'] ?? '',
            'sort_count' => $validatedData['sort_count'],
        ];

        $result = $this->productRepository->getProducts($params);

        return $this->getSuccessResponse('', $result);
    }

    /**
     * Загрузить все продукты
     * @return JsonResponse
     */
    public function getAllProducts(): JsonResponse {
        $result = $this->productRepository->getAllProducts();

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], $result['data'], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Создание продукта
     */
    public function store(ProductSaveRequest $request): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий для создания
        $result = $this->productRepository->createProduct($validatedData);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Обновить данные продукта
     */
    public function update(ProductSaveRequest $request, Product $product): JsonResponse {
        $validatedData = $request->validated();

        // Передаем данные в репозиторий
        $result = $this->productRepository->updateProduct($validatedData, $product);

        if ($result['success']) {
            return $this->getSuccessResponse($result['message'], [], $result['status_code']);
        }

        return $this->getErrorResponse($result['message'], [], $result['status_code']);
    }

    /**
     * Мягкое удаление продукта
     */
    public function destroy(Request $request, Product $product): JsonResponse {
        // Текущий авторизованный пользователь
        $currentUser = $request->user();
        if (!$product) {
            return $this->getErrorResponse('Product not found', [], 404);
        }

        $result = $this->productRepository->deleteProduct($currentUser, $product);

        if (!$result['success']) {
            return $this->getErrorResponse($result['message'], [], $result['status_code']);
        }

        return $this->getSuccessResponse($result['message'], [], $result['status_code']);
    }

}
