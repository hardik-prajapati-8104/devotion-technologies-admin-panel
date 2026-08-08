<?php

namespace App\Console\Commands;

use App\Models\BackupSetting;
use App\Services\BackupService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

/**
 * Register in app/Console/Kernel.php's schedule() method to run every
 * 5 minutes — the command itself decides, from admin-configured settings
 * in the database, whether "now" is actually the right time to run:
 *
 *   $schedule->command('backup:run-scheduled')->everyFiveMinutes();
 *
 * A fixed cron expression can't reflect a time the admin changes from a
 * settings page at runtime, so instead this polls frequently and checks
 * frequency/time/last-run-date itself, which is the standard pattern for
 * "user-configurable schedule" in Laravel without regenerating cron.
 */
class RunScheduledBackups extends Command
{
    protected $signature = 'backup:run-scheduled';
    protected $description = 'Runs the configured scheduled backup, if one is due right now.';

    public function handle(BackupService $service): int
    {
        $settings = BackupSetting::current();

        if (! $settings->schedule_enabled) {
            return self::SUCCESS;
        }

        if (! $this->isDue($settings)) {
            return self::SUCCESS;
        }

        $this->info('Running scheduled backup...');

        if ($settings->backup_database && $settings->backup_files) {
            $backup = $service->runFull(null, 'scheduled');
        } elseif ($settings->backup_database) {
            $backup = $service->runDatabase(null, 'scheduled');
        } elseif ($settings->backup_files) {
            $backup = $service->runFiles(null, 'scheduled');
        } else {
            $this->warn('Scheduled backup is enabled but neither database nor files backup is selected.');
            return self::SUCCESS;
        }

        $settings->update(['last_run_at' => now()]);

        $pruned = $service->prune();
        if ($pruned > 0) {
            $this->info("Pruned {$pruned} backup(s) past the retention window.");
        }

        if ($settings->notify_email) {
            $this->notify($settings->notify_email, $backup);
        }

        $this->info("Backup {$backup->status}: {$backup->filename}");

        return self::SUCCESS;
    }

    private function isDue(BackupSetting $settings): bool
    {
        $now = now();

        // Already run today/this week/this month — don't run twice.
        if ($settings->last_run_at) {
            $due = match ($settings->frequency) {
                'daily'   => ! $settings->last_run_at->isSameDay($now),
                'weekly'  => ! $settings->last_run_at->isSameWeek($now),
                'monthly' => ! $settings->last_run_at->isSameMonth($now),
            };
            if (! $due) {
                return false;
            }
        }

        // Frequency gate: weekly runs on Monday, monthly on the 1st.
        if ($settings->frequency === 'weekly' && $now->dayOfWeekIso !== 1) {
            return false;
        }
        if ($settings->frequency === 'monthly' && $now->day !== 1) {
            return false;
        }

        // Time-of-day gate: within the same 5-minute window as the
        // configured time, matching how often this command is polled.
        [$hour, $minute] = array_map('intval', explode(':', $settings->time));
        $scheduled = $now->copy()->setTime($hour, $minute, 0);

        return $now->diffInMinutes($scheduled, false) >= -5 && $now->diffInMinutes($scheduled, false) <= 0;
    }

    private function notify(string $email, $backup): void
    {
        try {
            Mail::raw(
                "Scheduled backup [{$backup->type}] finished with status: {$backup->status}\nFile: {$backup->filename}" .
                ($backup->error_message ? "\nError: {$backup->error_message}" : ''),
                fn ($msg) => $msg->to($email)->subject('Backup ' . $backup->status)
            );
        } catch (\Throwable $e) {
            $this->warn('Backup completed but the notification email failed to send: ' . $e->getMessage());
        }
    }
}
