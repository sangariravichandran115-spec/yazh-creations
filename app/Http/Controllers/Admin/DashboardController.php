<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Enquiry;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalProducts' => Product::count(),
            'activeProducts' => Product::active()->count(),
            'outOfStockProducts' => Product::where('stock_status', Product::STOCK_OUT_OF_STOCK)->count(),
            'totalCategories' => Category::count(),
            'newEnquiries' => Enquiry::where('status', Enquiry::STATUS_NEW)->count(),
            'totalEnquiries' => Enquiry::count(),
            'recentEnquiries' => Enquiry::with('product:id,name,slug')->latest()->limit(5)->get(),
        ]);
    }
}
