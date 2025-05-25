<?php

namespace App\Services;

use App\Models\Product;
use App\Traits\PaginationTrait;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use Plank\Mediable\Facades\MediaUploader;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductService
{
    use PaginationTrait;

    private Product $productObj;

    public function __construct()
    {
        $this->productObj = new Product;
    }

    public function collection(array $inputs)
    {
        $query = $this->productObj->newQuery();

        if (isset($inputs['category_id'])) {
            $query->where('category_id', $inputs['category_id']);
        }

        if (isset($inputs['status'])) {
            $query->where('status', $inputs['status']);
        }

        if (isset($inputs['search'])) {
            $query->where('name', 'like', '%' . $inputs['search'] . '%');
        }

        return $this->paginationAttribute($query, $inputs);
    }

    public function resource(int $id)
    {
        return $this->productObj->findOrFail($id);
    }

    public function create(array $inputs)
    {

        $product = $this->productObj->create($inputs);

        if (isset($inputs['media']) && request()->hasFile('media')) {
            $media = MediaUploader::fromSource(request()->file('media'))
                ->toDisk('public')
                ->toDirectory('products')
                ->upload();

            $product->syncMedia($media, 'featured'); // Sync the media to the product
        }

        return $product;
    }


    public function update(int $id, array $inputs)
    {
        $product = $this->productObj->findOrFail($id);

        if (!isset($inputs['media_id']) &&  request()->hasFile('media')) {

            $oldMedia = $product->getMedia('featured')->first();

            $media = MediaUploader::fromSource(request()->file('media'))
                ->toDisk('public')
                ->toDirectory('products')
                ->upload();

            $product->syncMedia($media, 'featured');

            if ($media) {
            }
            if ($oldMedia && $media) {
                $oldMedia->delete();
            }
        }

        $product->update($inputs);
        return $product;
    }

    public function delete(int $id): bool
    {
        $product = $this->productObj->findOrFail($id);
        if ($product->orderProducts()->exists()) {
            throw new CustomException('Cannot delete product as it has associated orders.');
        }
        $product->variants()->delete();

        if ($product->hasMedia('featured')) {
            $product->getMedia('featured')->each->delete(); // Delete the media file
        }

        return $product->delete();
    }
}