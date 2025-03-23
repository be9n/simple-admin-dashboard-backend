<?php

namespace App\Http\Resources\Admin\Products;

use App\Http\Resources\Admin\Categories\CategoryResource;
use App\Http\Resources\Admin\Media\MediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailedProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $images = $this->getMedia('images');

        return [
            'id' => $this->id,
            'name' => $this->name,
            'images' => $images ? MediaResource::collection($images) : null,
            'price' => (float) $this->price,
            'category' => CategoryResource::make($this->category),
        ];
    }
}
