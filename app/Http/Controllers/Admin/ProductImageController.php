<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(protected ImageService $images) {}

    public function destroy(Product $product, ProductImage $image): RedirectResponse
    {
        abort_if($image->product_id !== $product->id, 404);

        $this->images->delete($image);

        return back()->with('success', 'Image deleted successfully.');
    }

    public function setPrimary(Product $product, ProductImage $image, Request $request): RedirectResponse
    {
        abort_if($image->product_id !== $product->id, 404);

        $this->images->setPrimary($image);

        return back()->with('success', 'Primary image updated.');
    }
}
