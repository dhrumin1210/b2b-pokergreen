<?php

namespace App\Services;

use App\Models\Category;
use App\Traits\PaginationTrait;
use Plank\Mediable\Facades\MediaUploader;

class CategoryService
{
    use PaginationTrait;

    private Category $category;

    public function __construct()
    {
        $this->category = new Category;
    }

    public function collection(array $inputs)
    {
        $query = $this->category->getQB();

        if (isset($inputs['search'])) {
            $query->search($inputs['search']);
        }

        return $this->paginationAttribute($query, $inputs);
    }


    public function resource(int $id): Category
    {
        return $this->category->findOrFail($id);
    }


    public function create(array $inputs): Category
    {
        $inputs['status'] = $inputs['status'] ?? true;

        $category = $this->category->create($inputs);

        if (!isset($inputs['media_id']) &&  request()->hasFile('media')) {
            $media = MediaUploader::fromSource(request()->file('media'))
                ->toDisk('public')
                ->toDirectory('categories')
                ->upload();

            $category->attachMedia($media, 'featured'); // 'featured' = tag
        }

        return $category;
    }


    public function update(int $id, array $inputs): Category
    {
        $category = $this->resource($id);
        $category->update($inputs);


        if (request()->hasFile('media')) {
            // Get old media
            $oldMedia = $category->getMedia('featured')->first();

            // Upload new media
            $media = MediaUploader::fromSource(request()->file('media'))
                ->toDisk('public')
                ->toDirectory('categories')
                ->upload();

            // Associate the new media
            $category->syncMedia($media, 'featured');

            // Delete old media file from disk and DB
            if ($oldMedia && $media) {
                $oldMedia->delete(); // this deletes both DB record and physical file
            }
        }

        return $category;
    }

    public function delete(int $id): ?bool
    {
        $category = $this->resource($id);

        if ($category->hasMedia('featured')) {
            $category->getMedia('featured')->each->delete(); // Delete all media files
        }

        return $category->delete();
    }
}
