<?php

namespace App\Infrastructure\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class FileUploadService
{
    private string $disk;
    private string $basePath;
    
    public function __construct(string $disk = 'public', string $basePath = 'uploads')
    {
        $this->disk = $disk;
        $this->basePath = $basePath;
    }
    
    public function uploadFile(UploadedFile $file, string $directory = ''): string
    {
        $filename = $this->generateUniqueFilename($file);
        $path = $directory ? "{$this->basePath}/{$directory}" : $this->basePath;
        
        $uploadPath = $file->storeAs($path, $filename, $this->disk);
        
        return Storage::disk($this->disk)->url($uploadPath);
    }
    
    public function uploadAvatar(UploadedFile $file, string $userId): string
    {
        return $this->uploadFile($file, "avatars/{$userId}");
    }
    
    public function deleteFile(string $filePath): bool
    {
        try {
            return Storage::disk($this->disk)->delete($filePath);
        } catch (\Exception $e) {
            \Log::error('Failed to delete file', [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
    
    private function generateUniqueFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $basename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $timestamp = now()->format('YmdHis');
        $random = Str::random(8);
        
        return "{$basename}_{$timestamp}_{$random}.{$extension}";
    }
    
    public function getFileSize(string $filePath): int
    {
        return Storage::disk($this->disk)->size($filePath);
    }
    
    public function fileExists(string $filePath): bool
    {
        return Storage::disk($this->disk)->exists($filePath);
    }
}