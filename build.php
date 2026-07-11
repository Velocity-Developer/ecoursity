<?php

declare(strict_types=1);

/**
 * Ecoursity Build Script
 *
 * Creates a distribution zip of the plugin for deployment.
 * Run via: composer build:zip
 */

$root = __DIR__;
$distDir = $root . '/dist';
$zipPath = $distDir . '/ecoursity.zip';

if (! is_dir($distDir)) {
    mkdir($distDir, 0755, true);
}

$zip = new ZipArchive();

if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    fwrite(STDERR, "Failed to create zip: {$zipPath}" . PHP_EOL);
    exit(1);
}

// File to include (relative to plugin root)
$files = [
    'ecoursity.php',
    'LICENSE',
];

// Directories to include recursively
$dirs = [
    'app',
    'assets',
    'templates',
    'vendor',
];

foreach ($files as $file) {
    $path = $root . '/' . $file;
    if (file_exists($path)) {
        $zip->addFile($path, $file);
    }
}

foreach ($dirs as $dir) {
    $path = $root . '/' . $dir;
    if (is_dir($path)) {
        addDirToZip($zip, $path, $dir);
    }
}

$zip->close();

echo "✓ Zip created: {$zipPath}" . PHP_EOL;

// --- Helper ---

function addDirToZip(ZipArchive $zip, string $realPath, string $zipPath): void
{
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($realPath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        /** @var SplFileInfo $file */
        if (! $file->isFile()) {
            continue;
        }

        $realFilePath = $file->getRealPath();
        $relativePath = $zipPath . '/' . $file->getFilename();

        // Preserve subdirectory structure
        $subPath = substr($realFilePath, strlen($realPath) + 1);
        $relativePath = $zipPath . '/' . $subPath;

        $zip->addFile($realFilePath, $relativePath);
    }
}
