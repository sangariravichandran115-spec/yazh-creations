<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    private const CACHE_KEY = 'settings.all';

    /**
     * Get every setting as a key => value map.
     *
     * The whole map is cached as a single entry and memoized for the request,
     * so reading many settings costs at most one cache/DB round trip.
     *
     * @return array<string, string|null>
     */
    public static function map(): array
    {
        if (app()->bound(self::CACHE_KEY)) {
            return app(self::CACHE_KEY);
        }

        $settings = Cache::remember(
            self::CACHE_KEY,
            now()->addDay(),
            fn (): array => static::query()->pluck('value', 'key')->all(),
        );

        app()->instance(self::CACHE_KEY, $settings);

        return $settings;
    }

    /**
     * Get a setting value with an optional fallback.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        return static::map()[$key] ?? $default;
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCache());
        static::deleted(fn () => static::forgetCache());
    }

    protected static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        app()->forgetInstance(self::CACHE_KEY);
    }
}
