<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Backup;
use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BackupController extends Controller
{
    public function __construct(private BackupService $service)
    {
    }

    public function index(): View
    {
        $this->authorizeSecurity();

        return view('backend.backups.index', [
            'backups'  => Backup::latest()->paginate(15),
            'settings' => BackupSetting::current(),
        ]);
    }

    public function database(): RedirectResponse
    {
        $this->authorizeSecurity();

        $backup = $this->service->runDatabase(Auth::guard('admin')->user());
        $this->logAndRedirect($backup, 'Database backup');

        return back()->with(
            $backup->status === 'completed' ? 'success' : 'error',
            $backup->status === 'completed' ? 'Database backup completed.' : "Database backup failed: {$backup->error_message}"
        );
    }

    public function files(): RedirectResponse
    {
        $this->authorizeSecurity();

        $backup = $this->service->runFiles(Auth::guard('admin')->user());
        $this->logAndRedirect($backup, 'Files backup');

        return back()->with(
            $backup->status === 'completed' ? 'success' : 'error',
            $backup->status === 'completed' ? 'Files backup completed.' : "Files backup failed: {$backup->error_message}"
        );
    }

    public function full(): RedirectResponse
    {
        $this->authorizeSecurity();

        $backup = $this->service->runFull(Auth::guard('admin')->user());
        $this->logAndRedirect($backup, 'Full backup');

        return back()->with(
            $backup->status === 'completed' ? 'success' : 'error',
            $backup->status === 'completed' ? 'Full backup completed.' : "Full backup failed: {$backup->error_message}"
        );
    }

    public function download(Backup $backup): StreamedResponse
    {
        $this->authorizeSecurity();

        if (! $backup->exists()) {
            abort(404, 'Backup file no longer exists on disk.');
        }

        ActivityLog::record('backup-downloaded', 'Backup', Auth::guard('admin')->id(), "Downloaded backup {$backup->filename}.");

        return \Storage::disk($backup->disk)->download($backup->path());
    }

    public function restore(Request $request, Backup $backup): RedirectResponse
    {
        $this->authorizeSecurity();

        $request->validate(['confirm' => 'required|accepted']);

        if ($backup->status !== 'completed') {
            return back()->with('error', 'Only a completed backup can be restored.');
        }

        try {
            $this->service->restore($backup);
            ActivityLog::record('backup-restored', 'Backup', Auth::guard('admin')->id(), "Restored backup {$backup->filename}.");

            return back()->with('success', 'Backup restored successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function destroy(Backup $backup): RedirectResponse
    {
        $this->authorizeSecurity();

        \Storage::disk($backup->disk)->delete($backup->path());
        $backup->delete();

        ActivityLog::record('backup-deleted', 'Backup', Auth::guard('admin')->id(), "Deleted backup {$backup->filename}.");

        return back()->with('success', 'Backup deleted.');
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorizeSecurity();

        $data = $request->validate([
            'schedule_enabled' => 'nullable|boolean',
            'frequency'        => 'required|in:daily,weekly,monthly',
            'time'             => 'required|date_format:H:i',
            'retention_days'   => 'required|integer|min:0|max:3650',
            'backup_database'  => 'nullable|boolean',
            'backup_files'     => 'nullable|boolean',
            'file_paths'       => 'nullable|string',
            'notify_email'     => 'nullable|email',
        ]);

        foreach (['schedule_enabled', 'backup_database', 'backup_files'] as $flag) {
            $data[$flag] = $request->boolean($flag);
        }

        BackupSetting::current()->update($data);

        ActivityLog::record('backup-settings-updated', 'Backup', Auth::guard('admin')->id(), 'Updated backup settings.');

        return back()->with('success', 'Backup settings saved.');
    }

    public function prune(): RedirectResponse
    {
        $this->authorizeSecurity();

        $count = $this->service->prune();

        return back()->with('success', "Removed {$count} backup(s) past the retention window.");
    }

    private function logAndRedirect(Backup $backup, string $label): void
    {
        ActivityLog::record(
            'backup-created',
            'Backup',
            Auth::guard('admin')->id(),
            "{$label} " . ($backup->status === 'completed' ? 'completed' : 'failed') . ": {$backup->filename}"
        );
    }

    private function authorizeSecurity(): void
    {
        if (! Auth::guard('admin')->user()?->can('backups.manage')) {
            abort(403);
        }
    }
}