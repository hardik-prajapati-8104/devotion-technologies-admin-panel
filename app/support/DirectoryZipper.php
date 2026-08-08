<?php

namespace App\Support;

use ZipArchive;

class DirectoryZipper
{
    /**
     * Adds every file under $sourceDir to $zip, storing each entry at
     * $zipPathPrefix/<path-relative-to-source>, so a later extractTo()
     * back onto the project root reproduces the original layout exactly.
     */
    public static function addDirectory(ZipArchive $zip, string $sourceDir, string $zipPathPrefix): void
    {
        $sourceDir = rtrim($sourceDir, '/\\');

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relativePath = ltrim(str_replace($sourceDir, '', $item->getPathname()), '/\\');
            $zipPath = rtrim($zipPathPrefix, '/') . '/' . str_replace('\\', '/', $relativePath);

            if ($item->isDir()) {
                $zip->addEmptyDir($zipPath);
            } else {
                $zip->addFile($item->getPathname(), $zipPath);
            }
        }
    }
}
