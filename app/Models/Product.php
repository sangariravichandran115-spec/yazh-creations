<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['category_id', 'name', 'slug', 'description', 'price', 'discount_price', 'material', 'color', 'available_sizes', 'stock_status', 'featured', 'status'])]
class Product extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const STOCK_AVAILABLE = 'available';

    public const STOCK_OUT_OF_STOCK = 'out_of_stock';

    public const STOCK_COMING_SOON = 'coming_soon';

    public const STOCK_STATUSES = [
        self::STOCK_AVAILABLE => 'In Stock',
        self::STOCK_OUT_OF_STOCK => 'Out of Stock',
        self::STOCK_COMING_SOON => 'Coming Soon',
    ];

    public const SIZES = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'Free Size'];

    /**
     * Get the category that owns this product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the images for this product.
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    /**
     * Get the enquiries for this product.
     */
    public function enquiries(): HasMany
    {
        return $this->hasMany(Enquiry::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'discount_price' => 'decimal:2',
            'available_sizes' => 'array',
            'featured' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include active products.
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope a query to only include featured products.
     */
    #[Scope]
    protected function featured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * Scope a query to only include products with available stock.
     */
    #[Scope]
    protected function inStock(Builder $query): Builder
    {
        return $query->where('stock_status', self::STOCK_AVAILABLE);
    }

    /**
     * Get the effective (discounted) price.
     */
    public function sellingPrice(): string
    {
        return $this->discount_price ?: $this->price;
    }

    public function hasDiscount(): bool
    {
        return $this->discount_price !== null;
    }

    /**
     * Get the primary image model, falling back to the first image.
     */
    public function primaryImage(): ?ProductImage
    {
        $images = $this->images->sortBy('position')->values();

        return $images->firstWhere('is_primary', true) ?? $images->first();
    }

    /**
     * Get the URL of the primary image, or a placeholder when none exists.
     */
    public function imageUrl(): string
    {
        $image = $this->primaryImage();

        return $image ? $image->url() : asset('images/placeholder-product.jpg');
    }
}
