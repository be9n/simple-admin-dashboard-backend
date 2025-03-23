<?php

namespace App\Http\Resources\Admin\Products;

use App\Http\Resources\Admin\Categories\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $image = $this->getFirstMedia('images');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image_url' => $image ? $image->getUrl() : null,
            'price' => (float) $this->price,
            'category' => CategoryResource::make($this->category),
        ];
    }
}
