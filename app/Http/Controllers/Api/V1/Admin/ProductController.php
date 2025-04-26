<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Services\ProductService;
use App\Http\Controllers\Controller; 
// use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use App\Http\Resources\Product\Collection;
use App\Http\Resources\Product\Resource;
use App\Traits\ApiResponser;

class ProductController extends Controller
{
    use ApiResponser;
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->collection($request->all());
        return $this->collection(new Collection($products));
    }

    public function show($id)
    {
        $product = $this->productService->resource($id);
        return $this->resource(new Resource($product));
    }

    public function store(Request $request)
    {
        $product = $this->productService->create($request->all());
        return $this->resource(new Resource($product));
    }

    public function update(Request $request, $id)
    {
        $product = $this->productService->update($id, $request->all());
        return $this->resource(new Resource($product));
    }

    public function destroy($id)
    {
        $this->productService->delete($id);
        return response()->json(['message' => 'Product variant deleted successfully'], 200);
    }
}
