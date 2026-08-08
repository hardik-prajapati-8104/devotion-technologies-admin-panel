<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use App\Models\RecycleBinMeta;
use Illuminate\Console\Command;

class PurgeExpiredTrash extends Command
{
    protected $signature = 'recycle-bin:purge';

    protected $description = 'Permanently deletes trashed items past their auto-delete date. Archived items are never touched.';

    public function handle(): int
    {
        $registry = config('recycle-bin.models', []);

        $expired = RecycleBinMeta::trashed()
            ->whereNotNull('auto_delete_at')
            ->where('auto_delete_at', '<=', now())
            ->whereIn('recyclable_type', array_keys($registry))
            ->get();

        $deleted = 0;

        foreach ($expired as $meta) {
            $model = $meta->recyclable_type::withTrashed()->find($meta->recyclable_id);

            if ($model) {
                $model->forceDelete(); // Recyclable's deleting() hook cleans up the meta row itself
                $deleted++;
            } else {
                // Orphaned meta with no matching model row — clean it up directly.
                $meta->delete();
            }
        }

        if ($deleted > 0) {
            ActivityLog::record('purged', 'Recycle Bin', null, "Auto-deleted {$deleted} expired trash item(s).");
        }

        $this->info("Purged {$deleted} expired item(s) from the recycle bin.");
        return self::SUCCESS;
    }
}
