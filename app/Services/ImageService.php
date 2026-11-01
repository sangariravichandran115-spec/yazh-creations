<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class ImageService
{
    public function __construct(protected CloudinaryService $cloudinary) {}

    /**
     * Store uploaded product images and attach them to a product.
     *
     * @param  array<int, UploadedFile>  $files
     */
    public function attachUploadedImages(Product $product, array $files, ?int $primaryIndex = null): void
    {
        if (empty($files)) {
            return;
        }

        $nextPosition = $product->images()->max('position') ?? 0;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();
        $created = [];

        foreach ($files as $file) {
            $created[] = $product->images()->create([
                'path' => $this->storeUploadedImage($file, 'products', 'products'),
                'is_primary' => false,
                'position' => ++$nextPosition,
            ]);
        }

        if ($created === []) {
            return;
        }

        if ($primaryIndex !== null && isset($created[$primaryIndex])) {
            $product->images()->update(['is_primary' => false]);
            $created[$primaryIndex]->update(['is_primary' => true]);
        } elseif (! $hasPrimary) {
            $created[0]->update(['is_primary' => true]);
        }
    }

    public function storeUploadedImage(UploadedFile $file, string $localFolder, string $cloudinaryFolder): string
    {
        if ($this->cloudinary->enabled()) {
            try {
                $upload = $this->cloudinary->upload($file, $cloudinaryFolder);

                if ($upload !== null) {
                    return $upload['secure_url'];
                }
            } catch (Throwable $exception) {
                Log::warning('Cloudinary upload threw an exception.', [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $file->store($localFolder, 'public');
    }

    public function setPrimary(ProductImage $image): void
    {
        $product = $image->product()->firstOrFail();

        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
    }

    public function delete(ProductImage $image): void
    {
        $wasPrimary = $image->is_primary;
        $product = $image->product()->firstOrFail();

        $this->deleteStoredImage($image->path);
        $image->delete();

        if ($wasPrimary) {
            $fallback = $product->images()->orderBy('position')->first();

            if ($fallback) {
                $this->setPrimary($fallback);
            }
        }
    }

    public function deleteAll(Product $product): void
    {
        $product->images->each(fn (ProductImage $image) => $this->deleteStoredImage($image->path));
        $product->images()->delete();
    }

    protected function deleteStoredFile(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    public function deleteCategoryImage(Category $category): void
    {
        $this->deleteStoredImage($category->image);
    }

    public function deleteStoredImage(?string $path): void
    {
        if ($path === null || trim($path) === '') {
            return;
        }

        if (CloudinaryUrl::isCloudinary($path)) {
            $publicId = CloudinaryUrl::publicId($path);

            if ($publicId === null) {
                return;
            }

            try {
                $this->cloudinary->destroy($publicId);
            } catch (Throwable $exception) {
                Log::warning('Cloudinary destroy threw an exception.', [
                    'message' => $exception->getMessage(),
                    'public_id' => $publicId,
                ]);
            }

            return;
        }

        $this->deleteStoredFile($path);
    }

    /**
     * Generate a branded placeholder image with GD and return its relative storage path.
     *
     * @param  array{red: int, green: int, blue: int}  $background
     */
    public function createPlaceholder(
        string $label,
        array $background,
        string $directory = 'products',
        string $prefix = 'product',
        ?string $extension = null,
    ): string {
        $width = 800;
        $height = 1000;

        $image = imagecreatetruecolor($width, $height);

        $bg = imagecolorallocate($image, $background['red'], $background['green'], $background['blue']);
        $gold = imagecolorallocate($image, 184, 138, 59);
        $cream = imagecolorallocate($image, 248, 241, 231);

        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        imagesetthickness($image, 6);
        imagerectangle($image, 24, 24, $width - 25, $height - 25, $gold);

        $labelLines = wordwrap($label, 12, "\n", true);
        $font = 5;
        $fontWidth = imagefontwidth($font);
        $fontHeight = imagefontheight($font);

        foreach (explode("\n", $labelLines) as $index => $line) {
            $y = intdiv($height, 2) - intdiv($fontHeight * substr_count($labelLines, "\n"), 2) + ($index * $fontHeight);
            $x = intdiv($width - (mb_strlen($line) * $fontWidth), 2);

            imagestring($image, $font, $x, $y, $line, $cream);
        }

        imagestring($image, $font, 24, $height - 40, 'Yazh Creations', $gold);

        $filename = $prefix.'-'.Str::lower(Str::slug(substr($label, 0, 24))).'-'.Str::random(6).'.jpg';
        $path = $directory.'/'.$filename;

        Storage::disk('public')->makeDirectory($directory);

        $filePath = Storage::disk('public')->path($path);

        $success = imagejpeg($image, $filePath, 85);
        imagedestroy($image);

        if (! $success) {
            return '';
        }

        // @phpstan-ignore-next-line storage helper returns the relative path after makeDirectory
        return $path;
    }
}
