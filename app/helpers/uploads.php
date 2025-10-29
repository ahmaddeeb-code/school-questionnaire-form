<?php
namespace App\Helpers;

class Uploads
{
    private static array $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
    private static int $maxSize = 5 * 1024 * 1024;

    public static function handle(array $file, string $storagePath): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        if ($file['size'] > self::$maxSize) {
            return null;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        if (!in_array($mime, self::$allowedTypes, true)) {
            return null;
        }
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = rtrim($storagePath, '/') . '/' . $filename;
        if (!is_dir($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return $filename;
        }
        return null;
    }
}
