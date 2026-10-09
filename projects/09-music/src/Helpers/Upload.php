<?php

namespace App\Helpers;

/**
 * UPLOAD HELPER
 *
 * PHP CONCEPTS HERE:
 *  - $_FILES superglobal — populated when a form has enctype="multipart/form-data"
 *  - move_uploaded_file() — safe way to move a temp upload to its final destination
 *  - pathinfo() — extract extension from filename
 *  - uniqid() + mt_rand() — generate a collision-resistant unique filename
 *  - UPLOAD_ERR_OK — PHP constant (= 0) meaning no error occurred
 */
class Upload {

    // Allowed MIME types → maps to extension
    private static array $allowed = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    private static int $maxBytes = 5 * 1024 * 1024; // 5 MB

    /**
     * Handle a single file upload from $_FILES[$field].
     * Returns the saved filename on success, or throws RuntimeException on failure.
     * Returns null if no file was selected (empty upload).
     */
    public static function handle(string $field, string $destDir): ?string {
        if (!isset($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No file chosen — not an error
        }

        $file = $_FILES[$field];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload error code: ' . $file['error']);
        }

        if ($file['size'] > self::$maxBytes) {
            throw new \RuntimeException('File too large (max 5 MB).');
        }

        // Detect MIME from the actual file content, not the browser-supplied type
        $mime = mime_content_type($file['tmp_name']);
        if (!array_key_exists($mime, self::$allowed)) {
            throw new \RuntimeException('Invalid file type. Only JPG, PNG, GIF, WEBP allowed.');
        }

        $ext      = self::$allowed[$mime];
        $filename = uniqid('img_', true) . '_' . mt_rand(100, 999) . '.' . $ext;
        $destPath = rtrim($destDir, '/') . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            throw new \RuntimeException('Failed to save uploaded file.');
        }

        return $filename;
    }

    /**
     * Delete an old image file if it exists and isn't the placeholder.
     */
    public static function delete(string $filename, string $destDir): void {
        if ($filename && $filename !== 'placeholder.jpg') {
            $path = rtrim($destDir, '/') . '/' . $filename;
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }
}
