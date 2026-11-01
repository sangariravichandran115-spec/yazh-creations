<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    public function enabled(): bool
    {
        return (bool) config('services.cloudinary.enabled')
            && filled(config('services.cloudinary.cloud_name'))
            && filled(config('services.cloudinary.api_key'))
            && filled(config('services.cloudinary.api_secret'));
    }

    /**
     * @return array{secure_url: string, public_id: string}|null
     */
    public function upload(UploadedFile $file, string $folder): ?array
    {
        if (! $this->enabled()) {
            return null;
        }

        $stream = fopen($file->getRealPath(), 'r');

        if ($stream === false) {
            Log::warning('Cloudinary upload could not open the uploaded file.');

            return null;
        }

        $timestamp = now()->timestamp;
        $folder = $this->folder($folder);
        $params = ['folder' => $folder, 'timestamp' => $timestamp];

        try {
            $response = Http::connectTimeout(5)
                ->timeout(30)
                ->attach('file', $stream, $file->getClientOriginalName())
                ->post($this->endpoint('image/upload'), [
                    'api_key' => config('services.cloudinary.api_key'),
                    'folder' => $folder,
                    'signature' => $this->signature($params),
                    'timestamp' => $timestamp,
                ]);
        } finally {
            if (is_resource($stream)) {
                fclose($stream);
            }
        }

        if (! $response->successful()) {
            Log::warning('Cloudinary upload failed.', [
                'status' => $response->status(),
                'error' => $response->json('error.message'),
            ]);

            return null;
        }

        $payload = $response->json();

        if (! is_array($payload) || empty($payload['secure_url']) || empty($payload['public_id'])) {
            Log::warning('Cloudinary upload returned an invalid payload.');

            return null;
        }

        return [
            'secure_url' => (string) $payload['secure_url'],
            'public_id' => (string) $payload['public_id'],
        ];
    }

    public function destroy(string $publicId): bool
    {
        if (! $this->enabled() || $publicId === '') {
            return false;
        }

        $timestamp = now()->timestamp;
        $params = ['invalidate' => true, 'public_id' => $publicId, 'timestamp' => $timestamp];

        $response = Http::connectTimeout(5)
            ->timeout(30)
            ->asForm()
            ->post($this->endpoint('image/destroy'), [
                'api_key' => config('services.cloudinary.api_key'),
                'invalidate' => true,
                'public_id' => $publicId,
                'signature' => $this->signature($params),
                'timestamp' => $timestamp,
            ]);

        if (! $response->successful() || $response->json('result') !== 'ok') {
            Log::warning('Cloudinary destroy failed.', [
                'public_id' => $publicId,
                'result' => $response->json('result'),
                'status' => $response->status(),
            ]);

            return false;
        }

        return true;
    }

    protected function folder(string $folder): string
    {
        $base = trim((string) config('services.cloudinary.folder'), '/');
        $folder = trim($folder, '/');

        return trim($base.'/'.$folder, '/');
    }

    protected function endpoint(string $path): string
    {
        return sprintf(
            'https://api.cloudinary.com/v1_1/%s/%s',
            config('services.cloudinary.cloud_name'),
            ltrim($path, '/'),
        );
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function signature(array $params): string
    {
        ksort($params);

        return sha1(http_build_query($params, '', '&', PHP_QUERY_RFC3986).config('services.cloudinary.api_secret'));
    }
}
