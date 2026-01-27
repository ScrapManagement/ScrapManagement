<?php

namespace App\Services\Product;

use Illuminate\Support\Facades\Storage;

class ImageService
{

    public static function saveImages(array $images, string $folder = 'products'): array
    {
        $paths = [];

        foreach ($images as $image) {
            $name = uniqid() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs($folder, $name, 'public');
            $paths[] = $path;
        }

        return $paths;
    }

  
    public static function deleteImages($images): void
    {
        foreach ($images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->delete();
        }
    }
}
