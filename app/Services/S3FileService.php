<?php

// app/Services/S3FileService.php
namespace App\Services;

use App\Contracts\FileServiceInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class S3FileService implements FileServiceInterface
{
    protected string $disk = 's3';

    public function uploadFromUrl(string $url, string $directory = '', ?string $filename = null): string
    {
        $directory = trim($directory, '/');
        $tempFile = tempnam(sys_get_temp_dir(), 's3upload');
        
        try {
            file_put_contents($tempFile, file_get_contents($url));
            
            $file = new UploadedFile(
                $tempFile,
                $filename ?? basename($url),
                mime_content_type($tempFile),
                null,
                true
            );
            
            $path = $file->store($directory, $this->disk);
            
            if (!$path) {
                throw new \Exception('File upload failed or returned an empty path.');
            }
            
            return Storage::disk($this->disk)->url($path);
        } finally {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    public function delete(string $path): bool
    {
        $path = $this->normalizePath($path);
        return Storage::disk($this->disk)->delete($path);
    }

    protected function normalizePath(string $path): string
    {
        return str_contains($path, '.com/') ? explode('.com/', $path)[1] : $path;
    }
}