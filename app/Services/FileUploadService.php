<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class FileUploadService
{
    public static function upload(UploadedFile $file, string $directory, string $prefix): string
    {
        $fileName = time() . uniqid() . "_{$prefix}." . $file->getClientOriginalExtension();
        $file->move(public_path($directory), $fileName);
        return "{$directory}/{$fileName}";
    }
}
