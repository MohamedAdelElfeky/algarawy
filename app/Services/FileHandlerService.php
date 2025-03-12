<?php

namespace App\Services;

use App\Domain\Models\Image;
use App\Domain\Models\FilePdf;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Exception;

class FileHandlerService
{
    public function uploadSingleImage(Request $request, object $model, string $directory, string $prefix, string $field, string $type): void
{
    if (!$request->hasFile($field)) {
        return;
    }

    $file = $request->file($field);
    if ($file instanceof UploadedFile) {
        $filePath = $this->uploadFile($file, $directory, "{$prefix}_{$field}");
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


    public function attachImages(Request $request, object $model, string $directory, string $prefix): void
    {
       
        if (!$request->hasFile('images_or_video')) {
            return;
        }
        foreach ($request->file('images_or_video') as $file) { 
            if ($file instanceof UploadedFile) {           
                $filePath = $this->uploadFile($file, $directory, $prefix);
                $fullPath = public_path($filePath);
                $mimeType = mime_content_type($fullPath); 
                $imageType = pathinfo($fullPath, PATHINFO_EXTENSION);
                $model->images()->create([
                    'url'        => $filePath,
                    'mime'       => $mimeType,
                    'image_type' => $imageType,
                ]);
            }
        }
    }


    public function attachPdfs(Request $request, object $model, string $directory, string $prefix): void
    {
        if (!$request->hasFile('files')) {
            return;
        }

        foreach ($request->file('files') as $file) {
            if ($file instanceof UploadedFile) {
                $filePath = $this->uploadFile($file, $directory, $prefix);
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


    public function deleteFiles(array $deletedIds, string $type): void
    {
        $model = match ($type) {
            'image' => Image::class,
            'pdf'   => FilePdf::class,
            default => null,
        };

        if (!$model) {
            throw new Exception("Invalid file type: $type");
        }

        foreach ($deletedIds as $fileId) {
            $file = $model::find($fileId);
            if ($file) {
                Storage::delete($file->url);
                $file->delete();
            }
        }
    }


    private function uploadFile(UploadedFile $file, string $directory, string $prefix): string
    {
        try {
            return FileUploadService::upload($file, $directory, $prefix);
        } catch (Exception $e) {
            report($e);
            return '';
        }
    }
}
