<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\Storage;

class StorageService
{
    public function upload(string $path, $file)
    {
        return Storage::putFile($path, $file);
    }

    public function getFiles(string $path)
    {
        return Storage::files($path);
    }

    public function getFile(string $path)
    {
        return Storage::get($path);
    }

    public function deleteFile(string $path)
    {
        return Storage::delete($path);
    }

    public function downloadFile(string $path)
    {
        return Storage::download($path);
    }

    // Mocked methods for now

    public function initMultipartUpload(string $path)
    {
        return ['message' => 'Multipart upload initialized.'];
    }

    public function uploadPart(string $path, string $partId, $file)
    {
        return ['message' => 'Part uploaded.'];
    }

    public function completeMultipartUpload(string $path, array $parts)
    {
        return ['message' => 'Multipart upload completed.'];
    }

    public function shareFile(string $path, string $userId)
    {
        return ['message' => 'File shared.'];
    }
}
