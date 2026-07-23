<?php

declare(strict_types=1);

namespace Ecoursity\App\Services;

use RuntimeException;

class UploadService
{
    private const STORAGE_DIRECTORY = 'ecoursity-storage';
    public const PROFILE_DIRECTORY = 'profile';
    public const MATERI_DIRECTORY = 'materi';
    public const FILES_DIRECTORY = 'files';

    public function upload(array $file, string $subdirectory = ''): array
    {
        $this->validateUploadedFile($file);

        $storage = $this->storage($subdirectory);
        $this->ensureDirectoryExists($storage['path']);

        $filename = $this->uniqueFilename($storage['path'], (string) $file['name']);
        $target = trailingslashit($storage['path']) . $filename;

        if (! move_uploaded_file((string) $file['tmp_name'], $target)) {
            throw new RuntimeException('Failed to upload file.');
        }

        return $this->fileResponse($target);
    }

    public function uploadToSubfolder(array $file, string $subdirectory): array
    {
        return $this->upload($file, $subdirectory);
    }

    public function get(string $path): ?array
    {
        if (! is_dir($this->storage()['path'])) {
            return null;
        }

        $path = $this->resolvePath($path, true);

        if (! is_file($path)) {
            return null;
        }

        return $this->fileResponse($path);
    }

    public function delete(string $path): bool
    {
        if (! is_dir($this->storage()['path'])) {
            return false;
        }

        $path = $this->resolvePath($path, true);

        if (! is_file($path)) {
            return false;
        }

        return unlink($path);
    }

    private function validateUploadedFile(array $file): void
    {
        if (empty($file['tmp_name']) || empty($file['name'])) {
            throw new RuntimeException('No file was uploaded.');
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed with error code ' . (int) $file['error'] . '.');
        }

        if (! is_uploaded_file((string) $file['tmp_name'])) {
            throw new RuntimeException('Invalid uploaded file.');
        }
    }

    private function storage(string $directory = ''): array
    {
        $uploads = wp_upload_dir();

        if (! empty($uploads['error'])) {
            throw new RuntimeException((string) $uploads['error']);
        }

        $subdirectory = $this->sanitizeDirectory($directory);
        $relative = trim(self::STORAGE_DIRECTORY . '/' . $subdirectory, '/');

        return [
            'path' => trailingslashit($uploads['basedir']) . $relative,
            'url' => trailingslashit($uploads['baseurl']) . $relative,
        ];
    }

    private function sanitizeDirectory(string $directory): string
    {
        $directory = trim(str_replace('\\', '/', $directory), '/');
        $storageDirectory = self::STORAGE_DIRECTORY . '/';

        if ($directory === self::STORAGE_DIRECTORY) {
            return '';
        }

        if (str_starts_with($directory, $storageDirectory)) {
            $directory = substr($directory, strlen($storageDirectory));
        }

        if ($directory === '') {
            return '';
        }

        $segments = array_filter(explode('/', $directory), static function (string $segment): bool {
            return $segment !== '' && $segment !== '.' && $segment !== '..';
        });

        return implode('/', array_map('sanitize_file_name', $segments));
    }

    private function ensureDirectoryExists(string $path): void
    {
        if (is_dir($path)) {
            return;
        }

        if (! wp_mkdir_p($path)) {
            throw new RuntimeException('Failed to create upload directory.');
        }
    }

    private function uniqueFilename(string $directory, string $filename): string
    {
        return wp_unique_filename($directory, sanitize_file_name($filename));
    }

    private function resolvePath(string $path, bool $allowMissing = false): string
    {
        $storage = $this->storage();
        $base = wp_normalize_path($storage['path']);
        $path = wp_normalize_path(rawurldecode($path));

        if (str_starts_with($path, $storage['url'])) {
            $path = $base . '/' . ltrim(substr($path, strlen($storage['url'])), '/');
        } elseif (! str_starts_with($path, $base)) {
            $path = $base . '/' . ltrim($path, '/');
        }

        $realBase = realpath($base);
        $realPath = realpath($path);

        if ($realBase === false) {
            throw new RuntimeException('Invalid file path.');
        }

        if ($realPath === false && $allowMissing) {
            $realDirectory = realpath(dirname($path));

            if ($realDirectory !== false && str_starts_with(
                trailingslashit(wp_normalize_path($realDirectory)),
                trailingslashit(wp_normalize_path($realBase))
            )) {
                return $path;
            }

            $normalizedBase = trailingslashit(wp_normalize_path($realBase));
            $normalizedPath = wp_normalize_path($path);
            $segments = explode('/', substr($normalizedPath, strlen($normalizedBase)));

            if (str_starts_with($normalizedPath, $normalizedBase) && ! in_array('..', $segments, true)) {
                return $path;
            }
        }

        if ($realPath === false || ! str_starts_with(
            wp_normalize_path($realPath),
            trailingslashit(wp_normalize_path($realBase))
        )) {
            throw new RuntimeException('Invalid file path.');
        }

        return $realPath;
    }

    private function fileResponse(string $path): array
    {
        $storage = $this->storage();
        $base = trailingslashit(wp_normalize_path($storage['path']));
        $normalizedPath = wp_normalize_path($path);
        $relative = ltrim(substr($normalizedPath, strlen($base)), '/');

        return [
            'name' => basename($path),
            'path' => $relative,
            'full_path' => $path,
            'url' => trailingslashit($storage['url']) . str_replace('\\', '/', $relative),
            'mime_type' => wp_check_filetype($path)['type'] ?: 'application/octet-stream',
            'size' => filesize($path) ?: 0,
        ];
    }
}
