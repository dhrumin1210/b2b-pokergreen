<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Product\Collection;
use App\Http\Resources\Product\Resource;
use App\Services\ProductService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;

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
}
