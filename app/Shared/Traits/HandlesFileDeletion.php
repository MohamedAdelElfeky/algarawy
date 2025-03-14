<?php

namespace App\Shared\Traits;

use App\Domain\Models\Image;
use App\Domain\Models\FilePdf;
use Exception;
use Illuminate\Support\Facades\Storage;

trait HandlesFileDeletion
{
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
}