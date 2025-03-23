<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Categories\CategoryResource;
use App\Http\Resources\Admin\Products\ProductResource;
use App\Jobs\HeavyCalculationJob;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Database\Eloquent\Builder;

class CategoryController extends Controller
{

    public function list()
    {
        $categories = Category::get();

        return $this->successResponse('Processed Successfully', [
            'categories_list' => CategoryResource::collection($categories)
        ]);
    }
}
