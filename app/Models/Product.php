<?php

namespace App\Models;

use App\Traits\Filterable;
use App\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, Filterable, InteractsWithMedia, HasFile;

    protected $filterColumns = [
        'category_id' => [
            'column' => 'category.id',
        ],
        'price_from' => [
            'column' => 'price',
            'op' => '>='
        ],
        'price_to' => [
            'column' => 'price',
            'op' => '<='
        ],
    ];

    protected $fillable = [
        'name',
        'category_id',
        'price',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
