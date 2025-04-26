<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Category\Collection;
use App\Http\Resources\Category\Resource;
use App\Services\CategoryService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

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

    public function show(int $id)
    {
        $category = $this->categoryService->resource($id);
        return $this->resource(new Resource($category));
    }
}
