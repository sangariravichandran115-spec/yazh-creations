<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ProductService
{
    public function __construct(protected ImageService $images) {}

    /**
     * Build a filtered, searchable, sortable product query.
     *
     * @param  array{category?: int, search?: ?string, sort?: ?string, featured?: bool}  $filters
     */
    public function filteredQuery(array $filters = []): Builder
    {
        $query = Product::query()
            ->with(['category', 'images']);

        if (isset($filters['featured']) && $filters['featured']) {
            $query->featured();
        }

        if (isset($filters['category']) && is_numeric($filters['category'])) {
            $query->where('category_id', (int) $filters['category']);
        }

        if (! empty($filters['status']) && in_array($filters['status'], [Product::STATUS_ACTIVE, Product::STATUS_INACTIVE], true)) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $inner) use ($search): void {
                $inner->where('name', 'ilike', "%{$search}%")
                    ->orWhere('material', 'ilike', "%{$search}%")
                    ->orWhere('color', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        $this->applySort($query, $filters['sort'] ?? null);

        return $query;
    }

    /**
     * Get paginated public products.
     *
     * @param  array{category?: int, search?: ?string, sort?: ?string}  $filters
     */
    public function listForPublic(array $filters = []): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->active()
            ->paginate(12)
            ->withQueryString();
    }

    /**
     * Get paginated admin products including soft-deleted ones when requested.
     *
     * @param  array{search?: ?string, category?: int, status?: ?string}  $filters
     */
    public function listForAdmin(array $filters = []): LengthAwarePaginator
    {
        return $this->filteredQuery($filters)
            ->paginate(15)
            ->withQueryString();
    }

    /**
     * Get paginated trashed products.
     *
     * @param  array{search?: ?string}  $filters
     */
    public function listTrashed(array $filters = []): LengthAwarePaginator
    {
        $query = Product::onlyTrashed()
            ->with(['category'])
            ->latest('deleted_at');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('name', 'ilike', "%{$search}%");
        }

        return $query->paginate(15)->withQueryString();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function store(array $data): Product
    {
        $data['slug'] = $this->uniqueSlug($data['name']);
        $data['featured'] = filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $product = Product::create($data);

        $this->images->attachUploadedImages($product, $data['images'] ?? []);

        return $product;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): Product
    {
        $data['featured'] = filter_var($data['featured'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $product->update($data);

        $this->images->attachUploadedImages($product, $data['images'] ?? []);

        return $product;
    }

    public function softDelete(Product $product): void
    {
        $product->delete();
    }

    public function restore(Product $product): void
    {
        $product->restore();
    }

    public function forceDelete(Product $product): void
    {
        $this->images->deleteAll($product);
        $product->forceDelete();
    }

    /**
     * Related products from the same category, excluding the current product.
     *
     * @return Collection<int, Product>
     */
    public function related(Product $product, int $limit = 4): Collection
    {
        return Product::query()
            ->with(['category', 'images'])
            ->active()
            ->whereBelongsTo($product->category)
            ->whereKeyNot($product->id)
            ->latest()
            ->limit($limit)
            ->get();
    }

    protected function applySort(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'price_low', 'price-asc' => $query->orderBy('price'),
            'price_high', 'price-desc' => $query->orderByDesc('price'),
            'name' => $query->orderBy('name')->orderBy('id'),
            default => $query->latest('created_at')->latest('id'),
        };
    }

    protected function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name);

        $count = Product::withTrashed()
            ->where('slug', 'like', "{$slug}%")
            ->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }
}
