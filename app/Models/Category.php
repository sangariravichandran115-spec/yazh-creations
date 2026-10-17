<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;

#[Fillable(['name', 'slug', 'description', 'image', 'status'])]
class Category extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_INACTIVE = 'inactive';

    public const NAV_CACHE_KEY = 'nav_categories';

    /**
     * Get the products that belong to this category.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope a query to only include active categories.
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function hasProducts(): bool
    {
        return $this->products()->exists();
    }

    /**
     * Active categories for the navigation, cached and memoized for the request.
     *
     * @return Collection<int, static>
     */
    public static function nav(): Collection
    {
        if (app()->bound(self::NAV_CACHE_KEY)) {
            return app(self::NAV_CACHE_KEY);
        }

        $categories = Cache::remember(
            self::NAV_CACHE_KEY,
            now()->addHour(),
            fn (): Collection => static::query()->active()->orderBy('name')->get(),
        );

        app()->instance(self::NAV_CACHE_KEY, $categories);

        return $categories;
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetNavCache());
        static::deleted(fn () => static::forgetNavCache());
    }

    protected static function forgetNavCache(): void
    {
        Cache::forget(self::NAV_CACHE_KEY);
        app()->forgetInstance(self::NAV_CACHE_KEY);
    }
}
