<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\Setting;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());
        Paginator::useBootstrapFive();

        View::composer('*', function ($view): void {
            try {
                $settings = [
                    'brandName' => Setting::get('brand_name', 'Yazh Creations'),
                    'tagline' => Setting::get('tagline', 'Wear • Create • Inspire'),
                    'heroTitle' => Setting::get('hero_title', 'Wear • Create • Inspire'),
                    'heroDescription' => Setting::get('hero_description', 'Elegant women\'s fashion and handcrafted creations.'),
                    'aboutText' => Setting::get('about_text', ''),
                    'instagramUrl' => Setting::get('instagram_url', '#'),
                    'whatsappNumber' => Setting::get('whatsapp_number', ''),
                    'contactEmail' => Setting::get('email', ''),
                    'contactPhone' => Setting::get('phone', ''),
                    'contactAddress' => Setting::get('address', ''),
                    'storyTitle' => Setting::get('story_title', 'Our Story'),
                    'storyImage' => Setting::get('story_image', ''),
                    'navCategories' => Category::active()->orderBy('name')->get(),
                ];
            } catch (\Throwable) {
                $settings = [
                    'brandName' => 'Yazh Creations',
                    'tagline' => 'Wear • Create • Inspire',
                    'heroTitle' => 'Wear • Create • Inspire',
                    'heroDescription' => 'Elegant women\'s fashion and handcrafted creations.',
                    'aboutText' => '',
                    'instagramUrl' => '#',
                    'whatsappNumber' => '',
                    'contactEmail' => '',
                    'contactPhone' => '',
                    'contactAddress' => '',
                    'storyTitle' => 'Our Story',
                    'storyImage' => '',
                    'navCategories' => collect(),
                ];
            }

            $view->with($settings);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(Str::lower($request->string('email').'|'.$request->ip()));
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->string('email').'|'.$request->ip());
        });
    }
}
