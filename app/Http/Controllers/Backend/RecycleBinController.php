<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\RecycleBinMeta;
use App\Models\RecycleBinSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecycleBinController extends Controller
{
    public $user;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::guard('admin')->user();
            if (is_null($this->user) || ! $this->user->can('recycle-bin.view')) {
                abort(403, 'Sorry !! You are unauthorized to view the Recycle Bin !');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'trash') === 'archived' ? 'archived' : 'trashed';
        $registry = config('recycle-bin.models', []);

        $query = RecycleBinMeta::with(['deletedBy', 'archivedBy'])
            ->where('status', $status)
            ->whereIn('recyclable_type', array_keys($registry));

        if ($request->filled('type')) {
            $query->where('recyclable_type', $request->type);
        }

        $metaRows = $query->latest('deleted_at')->paginate(25)->withQueryString();

        // Resolve each meta row against its actual (withTrashed) model
        // instance, so the view can show a real title, not just an ID.
        $items = $metaRows->getCollection()->map(function ($meta) use ($registry) {
            $config = $registry[$meta->recyclable_type] ?? null;
            if (! $config) {
                return null;
            }

            $model = $meta->recyclable_type::withTrashed()->find(($meta->recyclable_id));
            if (! $model) {
                return null; // orphaned meta row (model hard-deleted outside the bin) — skip
            }

            $titleAttr = $config['title'];

            return (object) [
                'meta'  => $meta,
                'model' => $model,
                'type'  => $meta->recyclable_type,
                'label' => $config['label'],
                'icon'  => $config['icon'],
                'title' => $model->{$titleAttr} ?? "#{$model->id}",
            ];
        })->filter()->values();

        $metaRows->setCollection($items);

        $settings = RecycleBinSetting::current();

        return view('backend.recycle-bin.index', [
            'metaRows' => $metaRows,
            'status'   => $status,
            'registry' => $registry,
            'settings' => $settings,
        ]);
    }

    private function resolveModel(string $type, int $id)
    {
        $registry = config('recycle-bin.models', []);
        if (! isset($registry[$type])) {
            abort(404);
        }

        return $type::withTrashed()->findOrFail($id);
    }

    public function restore(Request $request)
    {
        $validated = $request->validate(['type' => 'required|string', 'id' => 'required|integer']);
        $model = $this->resolveModel($validated['type'], $validated['id']);

        $model->restore();

        ActivityLog::record('restored', class_basename($model), $model->id, "Restored \"{$this->titleOf($model)}\" from the recycle bin.");

        session()->flash('success', 'Item restored successfully !!');
        return back();
    }

    public function archive(Request $request)
    {
        $validated = $request->validate(['type' => 'required|string', 'id' => 'required|integer']);
        $model = $this->resolveModel($validated['type'], $validated['id']);

        $model->archiveRecord($this->user);

        ActivityLog::record('archived', class_basename($model), $model->id, "Archived \"{$this->titleOf($model)}\".");

        session()->flash('success', 'Item moved to Archive !!');
        return back();
    }

    public function unarchive(Request $request)
    {
        $validated = $request->validate(['type' => 'required|string', 'id' => 'required|integer']);
        $model = $this->resolveModel($validated['type'], $validated['id']);

        $model->unarchiveRecord();

        ActivityLog::record('unarchived', class_basename($model), $model->id, "Moved \"{$this->titleOf($model)}\" back to Trash.");

        session()->flash('success', 'Item moved back to Trash !!');
        return back();
    }

    public function forceDelete(Request $request)
    {
        if (! $this->user->can('recycle-bin.delete')) {
            abort(403, 'Sorry !! You are unauthorized to permanently delete items !');
        }

        $validated = $request->validate(['type' => 'required|string', 'id' => 'required|integer']);
        $model = $this->resolveModel($validated['type'], $validated['id']);
        $title = $this->titleOf($model);
        $typeLabel = class_basename($model);

        $model->forceDelete();

        ActivityLog::record('permanently deleted', $typeLabel, null, "Permanently deleted \"{$title}\".");

        session()->flash('success', 'Item permanently deleted !!');
        return back();
    }

    /**
     * Bulk actions from checkboxes in the list — same four operations,
     * applied to a batch of {type, id} pairs.
     */
    public function bulk(Request $request)
    {
        $validated = $request->validate([
            'action'        => 'required|in:restore,archive,unarchive,delete',
            'items'         => 'required|array|min:1',
            'items.*.type'  => 'required|string',
            'items.*.id'    => 'required|integer',
        ]);

        if ($validated['action'] === 'delete' && ! $this->user->can('recycle-bin.delete')) {
            abort(403);
        }

        $count = 0;
        foreach ($validated['items'] as $item) {
            try {
                $model = $this->resolveModel($item['type'], $item['id']);
            } catch (\Throwable $e) {
                continue;
            }

            match ($validated['action']) {
                'restore'   => $model->restore(),
                'archive'   => $model->archiveRecord($this->user),
                'unarchive' => $model->unarchiveRecord(),
                'delete'    => $model->forceDelete(),
            };
            $count++;
        }

        ActivityLog::record($validated['action'], 'Recycle Bin', null, "Bulk {$validated['action']}d {$count} item(s).");

        session()->flash('success', "{$count} item(s) updated !!");
        return back();
    }

    public function updateSettings(Request $request)
    {
        if (! $this->user->can('recycle-bin.manage')) {
            abort(403);
        }

        $validated = $request->validate([
            'default_retention_days' => 'required|integer|min:1|max:3650',
        ]);

        RecycleBinSetting::current()->update(array_merge($validated, ['updated_by' => $this->user->id]));

        session()->flash('success', 'Recycle bin settings updated !!');
        return back();
    }

    private function titleOf($model): string
    {
        $registry = config('recycle-bin.models', []);
        $titleAttr = $registry[get_class($model)]['title'] ?? 'id';

        return (string) ($model->{$titleAttr} ?? $model->id);
    }
}
