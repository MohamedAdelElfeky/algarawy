<?php

namespace App\Traits;

use App\Domain\Models\Image;
use App\Domain\Models\FilePdf;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Exception;

trait HasFileUploads
{
    /**
     * Attach images or videos to a model.
     */
    protected function attachImages(Request $request, object $model, string $directory, string $prefix): void
    {
        if (!$request->hasFile('images_or_video')) {
            return;
        }

        foreach ($request->file('images_or_video') as $file) {
            if ($file instanceof UploadedFile) {
                $filePath = $this->uploadFile($file, $directory, $prefix);
                $model->images()->create([
                    'url'        => $filePath,
                    'mime'       => $file->getMimeType(),
                    'image_type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
    }

    /**
     * Attach PDF files to a model.
     */
    protected function attachPdfs(Request $request, object $model, string $directory, string $prefix): void
    {
        if (!$request->hasFile('files')) {
            return;
        }

        foreach ($request->file('files') as $file) {
            if ($file instanceof UploadedFile) {
                $filePath = $this->uploadFile($file, $directory, $prefix);
                $model->pdfs()->create([
                    'url'  => $filePath,
                    'mime' => $file->getMimeType(),
                    'type' => $file->getClientOriginalExtension(),
                ]);
            }
        }
    }

    /**
     * Deletes files based on given IDs and type.
     */
    protected function deleteFiles(array $deletedIds, string $type): void
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

    /**
     * Handles file upload and returns file path.
     */
    private function uploadFile(UploadedFile $file, string $directory, string $prefix): string
    {
        try {
            $filePath = FileUploadService::upload($file, $directory, $prefix);
            return $filePath;
        } catch (Exception $e) {
            report($e);
            return '';
        }
    }
}
