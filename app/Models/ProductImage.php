<?php

namespace App\Models;

use App\Services\CloudinaryUrl;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['product_id', 'path', 'is_primary', 'position'])]
class ProductImage extends Model
{
    #[Scope]
    protected function primary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    /**
     * Get the product that owns this image.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'position' => 'integer',
        ];
    }

    public function url(?int $width = null, ?int $height = null): string
    {
        $url = CloudinaryUrl::resolve($this->path, $width, $height);

        return $url !== '' ? $url : asset('images/placeholder-product.jpg');
    }
}
