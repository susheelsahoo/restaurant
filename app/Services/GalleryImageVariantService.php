<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class GalleryImageVariantService
{
    public function getDisplayVariant(?string $imagePath, int $maxWidth = 1280, int $quality = 78): ?array
    {
        if (!$imagePath) {
            return null;
        }

        $disk = Storage::disk('public');

        if (!$disk->exists($imagePath)) {
            return null;
        }

        $sourceAbsolutePath = $disk->path($imagePath);
        $imageInfo = $this->readImageInfo($sourceAbsolutePath);

        if (!$imageInfo) {
            return [
                'url' => asset('storage/' . $imagePath),
                'width' => null,
                'height' => null,
            ];
        }

        [$sourceWidth, $sourceHeight, $sourceType] = $imageInfo;

        $optimizedRelativePath = $this->optimizedRelativePath($imagePath, $maxWidth);

        if (!$disk->exists($optimizedRelativePath)) {
            $this->createWebpVariant(
                $sourceAbsolutePath,
                $disk->path($optimizedRelativePath),
                $sourceType,
                $sourceWidth,
                $sourceHeight,
                $maxWidth,
                $quality
            );
        }

        [$displayWidth, $displayHeight] = $this->calculateTargetSize($sourceWidth, $sourceHeight, $maxWidth);

        return [
            'url' => asset('storage/' . $optimizedRelativePath) . '?v=' . @filemtime($disk->path($optimizedRelativePath)),
            'width' => $displayWidth,
            'height' => $displayHeight,
        ];
    }

    private function optimizedRelativePath(string $imagePath, int $maxWidth): string
    {
        $directory = trim(pathinfo($imagePath, PATHINFO_DIRNAME), '.');
        $filename = pathinfo($imagePath, PATHINFO_FILENAME);

        return ($directory ? $directory . '/' : '') . 'optimized/' . $filename . '-' . $maxWidth . '.webp';
    }

    public function deleteDisplayVariants(?string $imagePath, array $widths = [1280]): void
    {
        if (!$imagePath) {
            return;
        }

        $disk = Storage::disk('public');

        foreach ($widths as $width) {
            $optimizedRelativePath = $this->optimizedRelativePath($imagePath, $width);

            if ($disk->exists($optimizedRelativePath)) {
                $disk->delete($optimizedRelativePath);
            }
        }
    }

    private function readImageInfo(string $path): ?array
    {
        $info = @getimagesize($path);

        return $info ?: null;
    }

    private function calculateTargetSize(int $width, int $height, int $maxWidth): array
    {
        if ($width <= $maxWidth) {
            return [$width, $height];
        }

        $ratio = $maxWidth / $width;

        return [$maxWidth, (int) round($height * $ratio)];
    }

    private function createWebpVariant(
        string $sourcePath,
        string $targetPath,
        int $sourceType,
        int $sourceWidth,
        int $sourceHeight,
        int $maxWidth,
        int $quality
    ): void {
        $sourceImage = match ($sourceType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($sourcePath),
            IMAGETYPE_PNG => @imagecreatefrompng($sourcePath),
            IMAGETYPE_WEBP => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($sourcePath) : null,
            IMAGETYPE_GIF => @imagecreatefromgif($sourcePath),
            default => null,
        };

        if (!$sourceImage) {
            return;
        }

        [$targetWidth, $targetHeight] = $this->calculateTargetSize($sourceWidth, $sourceHeight, $maxWidth);

        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($targetImage, false);
        imagesavealpha($targetImage, true);

        $transparent = imagecolorallocatealpha($targetImage, 0, 0, 0, 127);
        imagefill($targetImage, 0, 0, $transparent);

        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $targetWidth,
            $targetHeight,
            $sourceWidth,
            $sourceHeight
        );

        $directory = dirname($targetPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        imagewebp($targetImage, $targetPath, $quality);

        imagedestroy($sourceImage);
        imagedestroy($targetImage);
    }
}
