<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(protected ProductService $products) {}

    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->string('search')->trim()->value() ?: null,
            'category' => $request->integer('category') ?: null,
            'status' => $request->string('status')->value() ?: null,
        ];

        return view('admin.products.index', [
            'products' => $this->products->listForAdmin($filters),
            'categories' => Category::all(),
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', [
            'categories' => Category::all(),
        ]);
    }

    public function store(ProductStoreRequest $request): RedirectResponse
    {
        $this->products->store($request->safe()->only([
            'category_id',
            'name',
            'description',
            'price',
            'discount_price',
            'material',
            'color',
            'available_sizes',
            'stock_status',
            'featured',
            'status',
        ]) + ['images' => $request->file('images', [])]);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        return view('admin.products.show', [
            'product' => $product,
        ]);
    }

    public function edit(Product $product): View
    {
        return view('admin.products.edit', [
            'product' => $product,
            'categories' => Category::all(),
        ]);
    }

    public function update(ProductUpdateRequest $request, Product $product): RedirectResponse
    {
        $this->products->update($product, $request->safe()->only([
            'category_id',
            'name',
            'description',
            'price',
            'discount_price',
            'material',
            'color',
            'available_sizes',
            'stock_status',
            'featured',
            'status',
        ]) + ['images' => $request->file('images', [])]);

        return redirect()
            ->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->softDelete($product);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product moved to trash.');
    }

    public function trash(Request $request): View
    {
        return view('admin.products.trash', [
            'products' => $this->products->listTrashed([
                'search' => $request->string('search')->trim()->value() ?: null,
            ]),
        ]);
    }

    public function restore(Product $trashedProduct): RedirectResponse
    {
        $this->products->restore($trashedProduct);

        return redirect()
            ->route('admin.products.trash')
            ->with('success', 'Product restored successfully.');
    }

    public function forceDelete(Product $trashedProduct): RedirectResponse
    {
        $this->products->forceDelete($trashedProduct);

        return redirect()
            ->route('admin.products.trash')
            ->with('success', 'Product permanently deleted.');
    }
}
