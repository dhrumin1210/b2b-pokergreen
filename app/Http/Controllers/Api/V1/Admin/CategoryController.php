<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Traits\ApiResponser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Category\Upsert;
use App\Http\Resources\Category\Collection;
use App\Http\Resources\Category\Resource;
use App\Services\CategoryService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    use ApiResponser;

    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function index(Request $request)
    {
        $categories = $this->categoryService->collection($request->all());

        return $this->collection(new Collection($categories));
    }

    public function store(Upsert $request)
    {
        $category = $this->categoryService->create($request->validated());

        return $this->resource(new Resource($category));
    }

    public function show(int $id)
    {
        $category = $this->categoryService->resource($id);

        return $this->resource(new Resource($category));
    }

    public function update(Upsert $request, int $id)
    {
        $category = $this->categoryService->update($id, $request->validated());

        return $this->resource(new Resource($category));
    }

    public function destroy(int $id)
    {
        $this->categoryService->delete($id);

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
