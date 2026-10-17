<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\ProductService;
use App\Services\WhatsAppService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request, ProductService $products): View
    {
        $categories = Category::active()->get();

        $sort = $request->string('sort')->value() ?: null;
        $featured = $sort === 'featured';
        $sort = $featured ? null : $sort;

        $filters = [
            'category' => $request->integer('category') ?: null,
            'search' => $request->string('search')->trim()->value() ?: null,
            'sort' => $sort,
            'featured' => $featured,
        ];

        return view('products.index', [
            'products' => $products->listForPublic($filters),
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function show(Product $product, ProductService $products, WhatsAppService $whatsapp): View
    {
        abort_if($product->status !== Product::STATUS_ACTIVE, 404);

        $relatedProducts = $products->related($product);

        return view('products.show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'whatsappLink' => $whatsapp->link($whatsapp->buildMessage($product)),
        ]);
    }
}
