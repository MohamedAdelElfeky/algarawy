<?php

namespace App\Shared\Traits;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

trait HandlesSingleImageUpload
{
    public function uploadSingleImage(Request $request, object $model, string $directory, string $prefix, string $field, string $type): void
    {
        if (!$request->hasFile($field)) {
            return;
        }

        $file = $request->file($field);
        if ($file instanceof UploadedFile) {
            $filePath = FileUploadService::upload($file, $directory, "{$prefix}_{$field}");
            $mimeType = mime_content_type(public_path($filePath));
            $imageType = pathinfo($filePath, PATHINFO_EXTENSION);

            $model->images()->create([
                'url'        => $filePath,
                'mime'       => $mimeType,
                'image_type' => $imageType,
                'type'       => $type, 
            ]);
        }
    }
}