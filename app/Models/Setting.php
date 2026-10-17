<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value'])]
class Setting extends Model
{
    /**
     * Get a setting value with an optional fallback, cached for the request lifetime.
     */
    public static function get(string $key, ?string $default = null): ?string
    {
        $value = Cache::remember(
            "setting.{$key}",
            now()->addDays(1),
            function () use ($key): ?string {
                return static::where('key', $key)->value('value');
            },
        );

        return $value ?? $default;
    }

    /**
     * Set a setting value and refresh the cached copy.
     */
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting.{$key}");
        Cache::remember(
            "setting.{$key}",
            now()->addDays(1),
            fn (): ?string => $value,
        );
    }
}
