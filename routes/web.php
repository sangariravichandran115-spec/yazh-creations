<?php

use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EnquiryController as AdminEnquiryController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/enquiry', [EnquiryController::class, 'store'])->name('enquiry.store');

Route::get('/sitemap.xml', function () {
    $products = Product::active()->get(['slug', 'updated_at']);
    $categories = Category::active()->get(['slug', 'updated_at']);

    $content = view('sitemap', compact('products', 'categories'))->render();

    return response($content, 200, [
        'Content-Type' => 'application/xml',
        'X-Robots-Tag' => 'noindex',
    ]);
})->name('sitemap');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'admin'])
    ->group(function (): void {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::bind('trashedProduct', fn (string $value) => Product::withTrashed()->where('id', $value)->firstOrFail());
        Route::get('/products/trash', [AdminProductController::class, 'trash'])->name('products.trash');
        Route::post('/products/{trashedProduct}/restore', [AdminProductController::class, 'restore'])->name('products.restore');
        Route::delete('/products/{trashedProduct}/force-delete', [AdminProductController::class, 'forceDelete'])->name('products.force-delete');

        Route::resource('products', AdminProductController::class);

        Route::delete('/products/{product}/images/{image}', [ProductImageController::class, 'destroy'])->name('products.images.destroy');
        Route::post('/products/{product}/images/{image}/primary', [ProductImageController::class, 'setPrimary'])->name('products.images.primary');

        Route::resource('categories', AdminCategoryController::class)->except(['show']);

        Route::get('/enquiries', [AdminEnquiryController::class, 'index'])->name('enquiries.index');
        Route::get('/enquiries/{enquiry}', [AdminEnquiryController::class, 'show'])->name('enquiries.show');
        Route::patch('/enquiries/{enquiry}/status', [AdminEnquiryController::class, 'updateStatus'])->name('enquiries.update-status');
        Route::delete('/enquiries/{enquiry}', [AdminEnquiryController::class, 'destroy'])->name('enquiries.destroy');

        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::patch('/settings', [SettingsController::class, 'update'])->name('settings.update');
    });
