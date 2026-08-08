<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Backup;
use App\Models\BackupSetting;
use App\Support\DatabaseDumper;
use App\Support\DirectoryZipper;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupService
{
    private const DISK = 'local'; // storage/app/backups — deliberately NOT the public disk, backups are never web-accessible directly

    public function __construct()
    {
        Storage::disk(self::DISK)->makeDirectory('backups');
    }

    public function runDatabase(?Admin $triggeredBy, string $source = 'manual'): Backup
    {
        $filename = 'db-' . now()->format('Y-m-d_His') . '.sql.gz';
        $backup = $this->startRecord('database', $filename, $triggeredBy, $source);

        try {
            DatabaseDumper::dump(Storage::disk(self::DISK)->path('backups/' . $filename));
            $this->completeRecord($backup);
        } catch (\Throwable $e) {
            $this->failRecord($backup, $e);
        }

        return $backup->fresh();
    }

    public function runFiles(?Admin $triggeredBy, string $source = 'manual'): Backup
    {
        $filename = 'files-' . now()->format('Y-m-d_His') . '.zip';
        $backup = $this->startRecord('files', $filename, $triggeredBy, $source);

        try {
            $this->zipConfiguredPaths(Storage::disk(self::DISK)->path('backups/' . $filename));
            $this->completeRecord($backup);
        } catch (\Throwable $e) {
            $this->failRecord($backup, $e);
        }

        return $backup->fresh();
    }

    public function runFull(?Admin $triggeredBy, string $source = 'manual'): Backup
    {
        $filename = 'full-' . now()->format('Y-m-d_His') . '.zip';
        $backup = $this->startRecord('full', $filename, $triggeredBy, $source);

        $tempDump = Storage::disk(self::DISK)->path('backups/.tmp-' . uniqid() . '.sql.gz');

        try {
            DatabaseDumper::dump($tempDump);

            $zip = new ZipArchive();
            $zipPath = Storage::disk(self::DISK)->path('backups/' . $filename);

            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Could not create backup zip.');
            }

            $zip->addFile($tempDump, 'database.sql.gz');

            foreach (BackupSetting::current()->filePathList() as $relativePath) {
                DirectoryZipper::addDirectory($zip, base_path($relativePath), 'files/' . trim($relativePath, '/\\'));
            }

            $zip->close();

            $this->completeRecord($backup);
        } catch (\Throwable $e) {
            $this->failRecord($backup, $e);
        } finally {
            if (file_exists($tempDump)) {
                unlink($tempDump);
            }
        }

        return $backup->fresh();
    }

    /**
     * Restores a backup in place. For 'database', replaces the current
     * database contents. For 'files', extracts back over the original
     * paths — THIS OVERWRITES CURRENT FILES; the controller requires an
     * explicit confirmation before calling this. For 'full', does both.
     */
    public function restore(Backup $backup): void
    {
        $path = Storage::disk($backup->disk)->path($backup->path());

        if (! file_exists($path)) {
            throw new \RuntimeException('Backup file is missing from disk.');
        }

        match ($backup->type) {
            'database' => DatabaseDumper::restore($path),
            'files'    => $this->extractZip($path, base_path()),
            'full'     => $this->restoreFull($path),
        };
    }

    private function restoreFull(string $zipPath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Could not open backup zip.');
        }

        $tempDir = Storage::disk(self::DISK)->path('backups/.restore-' . uniqid());
        $zip->extractTo($tempDir);
        $zip->close();

        $dumpFile = $tempDir . '/database.sql.gz';
        if (file_exists($dumpFile)) {
            DatabaseDumper::restore($dumpFile);
        }

        $filesDir = $tempDir . '/files';
        if (is_dir($filesDir)) {
            $this->copyDirectoryRecursive($filesDir, base_path());
        }

        $this->deleteDirectoryRecursive($tempDir);
    }

    private function extractZip(string $zipPath, string $destination): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Could not open backup zip.');
        }
        $zip->extractTo($destination);
        $zip->close();
    }

    /**
     * Deletes backup rows (and their files) older than the configured
     * retention window. retention_days = 0 means "keep forever" — skip
     * entirely rather than treating 0 as "delete everything older than
     * zero days", which would wipe every backup on the next run.
     */
    public function prune(): int
    {
        $settings = BackupSetting::current();

        if ($settings->retention_days <= 0) {
            return 0;
        }

        $old = Backup::where('created_at', '<', now()->subDays($settings->retention_days))->get();

        foreach ($old as $backup) {
            Storage::disk($backup->disk)->delete($backup->path());
            $backup->delete();
        }

        return $old->count();
    }

    private function zipConfiguredPaths(string $zipPath): void
    {
        $paths = BackupSetting::current()->filePathList();

        if (empty($paths)) {
            throw new \RuntimeException('No valid backup source directories are configured.');
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Could not create backup zip.');
        }

        foreach ($paths as $relativePath) {
            DirectoryZipper::addDirectory($zip, base_path($relativePath), trim($relativePath, '/\\'));
        }

        $zip->close();
    }

    private function startRecord(string $type, string $filename, ?Admin $triggeredBy, string $source): Backup
    {
        return Backup::create([
            'type'         => $type,
            'disk'         => self::DISK,
            'filename'     => $filename,
            'status'       => 'running',
            'source'       => $source,
            'triggered_by' => $triggeredBy?->id,
            'started_at'   => now(),
        ]);
    }

    private function completeRecord(Backup $backup): void
    {
        $fullPath = Storage::disk($backup->disk)->path($backup->path());

        $backup->update([
            'status'       => 'completed',
            'size'         => file_exists($fullPath) ? filesize($fullPath) : null,
            'completed_at' => now(),
        ]);
    }

    private function failRecord(Backup $backup, \Throwable $e): void
    {
        $backup->update([
            'status'        => 'failed',
            'error_message' => $e->getMessage(),
            'completed_at'  => now(),
        ]);
    }

    private function copyDirectoryRecursive(string $source, string $destination): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (! is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                copy($item->getPathname(), $target);
            }
        }
    }

    private function deleteDirectoryRecursive(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            $item->isDir() ? rmdir($item->getPathname()) : unlink($item->getPathname());
        }

        rmdir($dir);
    }
}