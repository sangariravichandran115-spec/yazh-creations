<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Store uploaded product images and attach them to a product.
     *
     * @param  array<int, UploadedFile>  $files
     */
    public function attachUploadedImages(Product $product, array $files): void
    {
        if (empty($files)) {
            return;
        }

        $nextPosition = $product->images()->max('position') ?? 0;
        $hasPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($files as $file) {
            $path = $file->store('products', 'public');

            $image = $product->images()->create([
                'path' => $path,
                'is_primary' => $hasPrimary ? false : true,
                'position' => ++$nextPosition,
            ]);

            $hasPrimary = $hasPrimary ?: $image->is_primary;
        }
    }

    public function setPrimary(ProductImage $image): void
    {
        $image->product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
    }

    public function delete(ProductImage $image): void
    {
        $wasPrimary = $image->is_primary;

        $this->deleteStoredFile($image->path);
        $image->delete();

        if ($wasPrimary) {
            $fallback = $image->product->images()->orderBy('position')->first();

            if ($fallback) {
                $this->setPrimary($fallback);
            }
        }
    }

    public function deleteAll(Product $product): void
    {
        $product->images->each(fn (ProductImage $image) => $this->deleteStoredFile($image->path));
        $product->images()->delete();
    }

    protected function deleteStoredFile(string $path): void
    {
        Storage::disk('public')->delete($path);
    }

    public function deleteCategoryImage(Category $category): void
    {
        if ($category->image) {
            $this->deleteStoredFile($category->image);
        }
    }

    public function deleteStoredImage(?string $path): void
    {
        if ($path) {
            $this->deleteStoredFile($path);
        }
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
