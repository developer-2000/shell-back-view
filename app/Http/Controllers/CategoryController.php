<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseController {

    /**
     * Get request to display list of categories
     * @return JsonResponse
     */
    public function index(): JsonResponse {
        $categories = Category::latest('id')
            ->get();

        return $this->getSuccessResponse('', compact("categories"));
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(StoreCategoryRequest $request): JsonResponse {
        $data = $request->validated();
        $category = Category::create($data);

        return $this->getSuccessResponse('Category created successfully', []);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(StoreCategoryRequest $request, Category $category): JsonResponse {
        $category->update($request->validated());

        return $this->getSuccessResponse('Category updated successfully', []);
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category): JsonResponse {
        if (!$category) {
            return $this->getErrorResponse('Category not found', [], 404);
        }

        $category->delete();

        return $this->getSuccessResponse('Category deleted successfully', []);
    }

}
