<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(Category $category, Request $request, ProductService $products): View
    {
        abort_if($category->status !== Category::STATUS_ACTIVE, 404);

        abort_unless($category->products()->exists(), 404);

        $sort = $request->string('sort')->value() ?: null;
        $featured = $sort === 'featured';
        $sort = $featured ? null : $sort;

        $filters = [
            'category' => $category->id,
            'search' => $request->string('search')->trim()->value() ?: null,
            'sort' => $sort,
            'featured' => $featured,
        ];

        return view('categories.show', [
            'category' => $category,
            'products' => $products->listForPublic($filters),
            'filters' => $filters,
        ]);
    }
}
