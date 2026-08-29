<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaStorage
{
    public const DISK_PUBLIC = 'public';
    public const DISK_PRIVATE = 'local';

    public const ALLOWED_IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/webp', 'image/avif'];
    public const ALLOWED_DOCUMENT_MIMES = ['application/pdf', 'image/jpeg', 'image/png'];

    public const MAX_IMAGE_SIZE_KB = 5120; // 5 MB
    public const MAX_DOCUMENT_SIZE_KB = 10240; // 10 MB

    /**
     * Store a public image (e.g. property photo, unit photo, avatar).
     */
    public static function storePublicImage(UploadedFile $file, string $folder = 'properties'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($folder, $filename, self::DISK_PUBLIC);
    }

    /**
     * Store a private document (e.g. rental contract, ID card, invoice pdf).
     */
    public static function storePrivateDocument(UploadedFile $file, string $folder = 'contracts'): string
    {
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs($folder, $filename, self::DISK_PRIVATE);
    }

    /**
     * Get the public URL for a stored file, or fallback placeholder.
     */
    public static function publicUrl(?string $path, ?string $fallback = null): string
    {
        if (empty($path)) {
            return $fallback ?? asset('images/placeholders/property.jpg');
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return Storage::disk(self::DISK_PUBLIC)->url($path);
    }

    /**
     * Check if a file exists on the specified disk.
     */
    public static function exists(string $path, string $disk = self::DISK_PUBLIC): bool
    {
        return Storage::disk($disk)->exists($path);
    }

    /**
     * Delete a file safely from disk.
     */
    public static function delete(?string $path, string $disk = self::DISK_PUBLIC): bool
    {
        if (empty($path)) {
            return false;
        }

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return false;
    }
}
