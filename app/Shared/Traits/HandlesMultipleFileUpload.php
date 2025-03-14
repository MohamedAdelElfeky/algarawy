<?php

namespace App\Shared\Traits;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

trait HandlesMultipleFileUpload
{
    public function attachFiles(Request $request, object $model, string $directory, string $prefix): void
    {
        if (!$request->hasFile('files')) {
            return;
        }

        foreach ($request->file('files') as $file) {
            if ($file instanceof UploadedFile) {
                $filePath = FileUploadService::upload($file, $directory, $prefix);
                $fullPath = public_path($filePath);
                $mimeType = mime_content_type($fullPath);
                $imageType = pathinfo($fullPath, PATHINFO_EXTENSION);
                $model->pdfs()->create([
                    'url'  => $filePath,
                    'mime'       => $mimeType,
                    'image_type' => $imageType,
                ]);
            }
        }
    }
}
