<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Products\ProductCreateRequest;
use App\Http\Requests\Admin\Products\ProductUpdateRequest;
use App\Http\Resources\Admin\Products\DetailedProductResource;
use App\Http\Resources\Admin\Products\ProductResource;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ProductController extends Controller
{
    public function index()
    {
        $search = request('search');
        $sortBy = request('sort_by');
        $sortDir = request('sort_dir');

        $products = Product::with(['category', 'media'])
            ->when($sortBy, fn(Builder $q) => $q->orderBy($sortBy, $sortDir ?? 'asc'))
            ->when($search, fn(Builder $q) => $q->where('name', 'like', "%$search%"))
            ->filter()
            ->paginate();

        $paginationData = [
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total_records' => $products->total(),
            'has_more_pages' => $products->hasMorePages(),
            'has_pages' => $products->hasPages(),
            'has_next_page' => $products->currentPage() < $products->lastPage(),
            'has_prev_page' => $products->currentPage() > 1,
            'path' => $products->path(),
            'next_page_url' => $products->nextPageUrl(),
            'prev_page_url' => $products->previousPageUrl(),
        ];

        return $this->successResponse('Processed Successfully!', [
            'products' => ProductResource::collection($products),
            'pagination' => $paginationData
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['category', 'media']);
        return $this->successResponse('Processed Successfully', [
            'product' => DetailedProductResource::make($product)
        ]);
    }

    public function store(ProductCreateRequest $request)
    {
        $validated = $request->validated();
        $product = Product::create($validated);

        if (@$validated['images']) {
            $product->storeMultipleFiles($validated['images']);
        }

        return $this->successResponse('Created Successfully');
    }

    public function update(ProductUpdateRequest $request, Product $product)
    {
        $validated = $request->validated();

        $product->update($validated);

        if (@$validated['images']) {
            $product->storeMultipleFiles($validated['images']);
        }

        return $this->successResponse('Updated Successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return $this->successResponse('Deleted Successfully');
    }

}
