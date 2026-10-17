<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Setting;
use App\Services\ProductService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(ProductService $products): View
    {
        $featuredCategories = Category::active()
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $newArrivals = $products->filteredQuery()
            ->active()
            ->latest()
            ->limit(8)
            ->get();

        $featuredProducts = $products->filteredQuery(['featured' => true])
            ->active()
            ->limit(8)
            ->get();

        return view('home.index', [
            'featuredCategories' => $featuredCategories,
            'newArrivals' => $newArrivals,
            'featuredProducts' => $featuredProducts,
            'brandName' => Setting::get('brand_name', 'Yazh Creations'),
            'tagline' => Setting::get('tagline', 'Wear • Create • Inspire'),
            'heroTitle' => Setting::get('hero_title', 'Wear • Create • Inspire'),
            'heroDescription' => Setting::get('hero_description', 'Elegant women\'s fashion and handcrafted creations.'),
            'aboutText' => Setting::get('about_text', ''),
            'storyTitle' => Setting::get('story_title', 'Our Story'),
            'storyImage' => Setting::get('story_image', ''),
            'instagramUrl' => Setting::get('instagram_url', '#'),
        ]);
    }

    public function about(): View
    {
        return view('about.index', [
            'brandName' => Setting::get('brand_name', 'Yazh Creations'),
            'tagline' => Setting::get('tagline', 'Wear • Create • Inspire'),
            'aboutText' => Setting::get('about_text', ''),
            'storyTitle' => Setting::get('story_title', 'Our Story'),
            'storyImage' => Setting::get('story_image', ''),
        ]);
    }
}
