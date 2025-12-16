<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trait for handling file uploads
 * Provides common file upload functionality
 */
trait HandlesFileUploads
{
    /**
     * Upload an image file
     *
     * @param Request $request
     * @param string $fieldName Field name in request
     * @param string $storagePath Storage path (e.g., 'templates/previews')
     * @param string $disk Storage disk (default: 'public')
     * @param int $maxSize Max file size in KB (default: 2048)
     * @return string|null Uploaded file name or null
     */
    protected function uploadImage(
        Request $request,
        string $fieldName,
        string $storagePath,
        string $disk = 'public',
        int $maxSize = 2048
    ): ?string {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $file = $request->file($fieldName);

        // Validate file (SVG is not an image type, so we handle it separately)
        $mimeType = $file->getMimeType();
        $isSvg = $mimeType === 'image/svg+xml' || $file->getClientOriginalExtension() === 'svg';

        if ($isSvg) {
            $request->validate([
                $fieldName => [
                    'file',
                    'mimes:svg',
                    'max:' . $maxSize,
                ],
            ]);
        } else {
            $request->validate([
                $fieldName => [
                    'image',
                    'mimes:jpeg,png,jpg,gif,webp',
                    'max:' . $maxSize,
                ],
            ]);
        }

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(10) . '.' . $extension;

        // Store file
        $file->storeAs($storagePath, $fileName, $disk);

        return $fileName;
    }

    /**
     * Upload a general file
     *
     * @param Request $request
     * @param string $fieldName Field name in request
     * @param string $storagePath Storage path
     * @param string $disk Storage disk (default: 'public')
     * @param array $allowedMimes Allowed MIME types
     * @param int $maxSize Max file size in KB
     * @return string|null Uploaded file name or null
     */
    protected function uploadFile(
        Request $request,
        string $fieldName,
        string $storagePath,
        string $disk = 'public',
        array $allowedMimes = ['pdf', 'doc', 'docx'],
        int $maxSize = 5120
    ): ?string {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $file = $request->file($fieldName);

        // Validate file
        $request->validate([
            $fieldName => [
                'file',
                'mimes:' . implode(',', $allowedMimes),
                'max:' . $maxSize,
            ],
        ]);

        // Generate unique filename
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '_' . Str::random(10) . '.' . $extension;

        // Store file
        $file->storeAs($storagePath, $fileName, $disk);

        return $fileName;
    }

    /**
     * Delete a file from storage
     *
     * @param string $filePath File path relative to storage disk
     * @param string $disk Storage disk (default: 'public')
     * @return bool
     */
    protected function deleteFile(string $filePath, string $disk = 'public'): bool
    {
        if (empty($filePath)) {
            return false;
        }

        try {
            return Storage::disk($disk)->delete($filePath);
        } catch (\Exception $e) {
            \Log::error("Failed to delete file: {$filePath}", ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Move uploaded file to a specific directory (for non-storage files)
     *
     * @param Request $request
     * @param string $fieldName Field name in request
     * @param string $targetDirectory Target directory path
     * @param string|null $customFileName Custom file name (without extension)
     * @return string|null File name or null
     */
    protected function moveFileToDirectory(
        Request $request,
        string $fieldName,
        string $targetDirectory,
        ?string $customFileName = null
    ): ?string {
        if (!$request->hasFile($fieldName)) {
            return null;
        }

        $file = $request->file($fieldName);

        // Create directory if it doesn't exist
        if (!file_exists($targetDirectory)) {
            mkdir($targetDirectory, 0755, true);
        }

        // Generate filename
        if ($customFileName) {
            $extension = $file->getClientOriginalExtension();
            $fileName = $customFileName . '.' . $extension;
        } else {
            $fileName = $file->getClientOriginalName();
        }

        // Move file
        $file->move($targetDirectory, $fileName);

        return $fileName;
    }

    /**
     * Delete file from directory (non-storage)
     *
     * @param string $filePath Full file path
     * @return bool
     */
    protected function deleteFileFromDirectory(string $filePath): bool
    {
        if (empty($filePath) || !file_exists($filePath)) {
            return false;
        }

        try {
            return unlink($filePath);
        } catch (\Exception $e) {
            \Log::error("Failed to delete file from directory: {$filePath}", ['error' => $e->getMessage()]);
            return false;
        }
    }
}
