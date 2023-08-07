<?php

namespace App\Services;

use App\Models\Image;

class ImageService
{
    public function uploadImage($url, $modelType, $modelId, $imageType, $mime)
    {
        return Image::create([
            'url' => $url,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'image_type' => $imageType,
            'mime' => $mime,
        ]);
    }

    public function deleteImage($imageId)
    {
        return Image::findOrFail($imageId)->delete();
    }
}
