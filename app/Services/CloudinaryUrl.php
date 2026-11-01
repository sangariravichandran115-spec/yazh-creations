<?php

namespace App\Services;

class CloudinaryUrl
{
    private const HOST = 'res.cloudinary.com';

    public static function isCloudinary(?string $url): bool
    {
        return is_string($url) && str_contains($url, '://'.self::HOST.'/');
    }

    public static function isRemote(?string $path): bool
    {
        return is_string($path) && (str_starts_with($path, 'http://') || str_starts_with($path, 'https://'));
    }

    public static function resolve(?string $path, ?int $width = null, ?int $height = null): string
    {
        if ($path === null || trim($path) === '') {
            return '';
        }

        $path = trim($path);

        if (self::isCloudinary($path)) {
            return self::transform($path, $width, $height);
        }

        if (self::isRemote($path)) {
            return $path;
        }

        return asset('storage/'.ltrim($path, '/'));
    }

    public static function secure(string $url): string
    {
        if (str_starts_with($url, 'http://')) {
            return 'https://'.substr($url, 7);
        }

        return $url;
    }

    public static function transform(string $url, ?int $width = null, ?int $height = null): string
    {
        if (! self::isCloudinary($url)) {
            return $url;
        }

        $url = self::secure($url);
        $transformation = 'f_auto,q_auto';

        if ($width !== null && $height !== null) {
            $transformation .= sprintf(',c_fill,w_%d,h_%d', $width, $height);
        } elseif ($width !== null) {
            $transformation .= sprintf(',c_limit,w_%d', $width);
        } elseif ($height !== null) {
            $transformation .= sprintf(',c_limit,h_%d', $height);
        }

        return preg_replace(
            '#(/image/upload/)(?!f_auto|q_auto)#',
            '$1'.$transformation.'/',
            $url,
            1,
        ) ?? $url;
    }

    public static function publicId(?string $url): ?string
    {
        if (! self::isCloudinary($url)) {
            return null;
        }

        $path = parse_url(self::secure((string) $url), PHP_URL_PATH);

        if (! is_string($path) || preg_match('#/image/upload/(?:v\d+/)?(.+)$#', $path, $matches) !== 1) {
            return null;
        }

        $publicId = preg_replace('/\.[a-zA-Z0-9]+$/', '', $matches[1]);

        return is_string($publicId) && $publicId !== '' ? $publicId : null;
    }
}
