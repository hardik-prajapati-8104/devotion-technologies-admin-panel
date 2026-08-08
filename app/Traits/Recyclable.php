<?php

namespace App\Traits;

use App\Models\Admin;
use App\Models\RecycleBinMeta;
use App\Models\RecycleBinSetting;
use Illuminate\Support\Facades\Auth;

/**
 * Add this trait ALONGSIDE Illuminate\Database\Eloquent\SoftDeletes to
 * any model that should go through Active → Trash → Archived →
 * Permanent Delete instead of vanishing on delete().
 *
 * Requires the model's own table to have a `deleted_at` column (a
 * normal soft-deletes migration) — everything else (who deleted it,
 * trash vs archived, auto-delete date) lives in the shared
 * recycle_bin_meta table, so no other schema changes are needed per model.
 */
trait Recyclable
{
    protected static function bootRecyclable(): void
    {
        static::deleting(function ($model) {
            // A force-delete (permanent) skips the trash entirely —
            // just clean up the ledger row and let the DB row actually go.
            if (method_exists($model, 'isForceDeleting') && $model->isForceDeleting()) {
                RecycleBinMeta::where('recyclable_type', get_class($model))
                    ->where('recyclable_id', $model->id)
                    ->delete();

                return;
            }

            RecycleBinMeta::updateOrCreate(
                ['recyclable_type' => get_class($model), 'recyclable_id' => $model->id],
                [
                    'status'         => 'trashed',
                    'deleted_by'     => Auth::guard('admin')->id(),
                    'deleted_at'     => now(),
                    'archived_by'    => null,
                    'archived_at'    => null,
                    'auto_delete_at' => now()->addDays($model->recycleRetentionDays()),
                ]
            );

            // Cascade-trash whichever child relations this model declares
            // (see recyclableRelations() below) — e.g. deleting a Task
            // also soft-deletes its comments/attachments/checklists.
            foreach ($model->recyclableRelations() as $relation) {
                $model->{$relation}()->get()->each->delete();
            }
        });

        static::restoring(function ($model) {
            // Bring back child records that were cascade-trashed with
            // this one. Restores ALL currently-trashed records under
            // this relation — see the class-level note on the one
            // known edge case this simplification accepts.
            foreach ($model->recyclableRelations() as $relation) {
                $relatedQuery = $model->{$relation}();
                $relatedModel = $relatedQuery->getRelated();

                if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($relatedModel))) {
                    $foreignKey = method_exists($relatedQuery, 'getForeignKeyName')
                        ? $relatedQuery->getForeignKeyName()
                        : null;

                    if ($foreignKey) {
                        $relatedModel::onlyTrashed()->where($foreignKey, $model->id)->restore();
                    }
                }
            }

            RecycleBinMeta::where('recyclable_type', get_class($model))
                ->where('recyclable_id', $model->id)
                ->delete();
        });
    }

    /**
     * Override in the model to list HasMany relation method names that
     * should be trashed/restored together with this record. Leave empty
     * (default) if the model has no child records worth cascading.
     *
     * Example (on Task): return ['comments', 'attachments', 'checklists'];
     */
    public function recyclableRelations(): array
    {
        return [];
    }

    public function recycleMeta(): ?RecycleBinMeta
    {
        return RecycleBinMeta::where('recyclable_type', static::class)
            ->where('recyclable_id', $this->id)
            ->first();
    }

    public function recycleRetentionDays(): int
    {
        return RecycleBinSetting::retentionDaysFor(static::class);
    }

    public function archiveRecord(Admin $admin): void
    {
        $meta = $this->recycleMeta();

        if ($meta) {
            $meta->update([
                'status'         => 'archived',
                'archived_by'    => $admin->id,
                'archived_at'    => now(),
                'auto_delete_at' => null, // archiving pauses the auto-delete countdown
            ]);
        }
    }

    public function unarchiveRecord(): void
    {
        $meta = $this->recycleMeta();

        if ($meta) {
            $meta->update([
                'status'         => 'trashed',
                'archived_by'    => null,
                'archived_at'    => null,
                'auto_delete_at' => now()->addDays($this->recycleRetentionDays()),
            ]);
        }
    }

    public function isArchived(): bool
    {
        return $this->recycleMeta()?->status === 'archived';
    }
}
