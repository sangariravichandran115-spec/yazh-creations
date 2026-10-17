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
                $settings = Setting::map();

                $shared = [
                    'brandName' => $settings['brand_name'] ?? 'Yazh Creations',
                    'tagline' => $settings['tagline'] ?? 'Wear • Create • Inspire',
                    'heroTitle' => $settings['hero_title'] ?? 'Wear • Create • Inspire',
                    'heroDescription' => $settings['hero_description'] ?? 'Elegant women\'s fashion and handcrafted creations.',
                    'aboutText' => $settings['about_text'] ?? '',
                    'instagramUrl' => $settings['instagram_url'] ?? '#',
                    'whatsappNumber' => $settings['whatsapp_number'] ?? '',
                    'contactEmail' => $settings['email'] ?? '',
                    'contactPhone' => $settings['phone'] ?? '',
                    'contactAddress' => $settings['address'] ?? '',
                    'storyTitle' => $settings['story_title'] ?? 'Our Story',
                    'storyImage' => $settings['story_image'] ?? '',
                    'navCategories' => Category::nav(),
                ];
            } catch (\Throwable) {
                $shared = [
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

            $view->with($shared);
        });

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(Str::lower($request->string('email').'|'.$request->ip()));
        });

        RateLimiter::for('password-reset', function (Request $request) {
            return Limit::perMinute(3)->by($request->string('email').'|'.$request->ip());
        });
    }
}
