<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'admin_id',
        'action',
        'module',
        'record_id',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Helper for quick logging from anywhere in the app:
     * ActivityLog::record('updated', 'Projects', $project->id, 'Updated project "X"');
     */
    public static function record(string $action, string $module, ?int $recordId, string $description): self
    {
        return self::create([
            'admin_id'    => auth('admin')->id(),
            'action'      => $action,
            'module'      => $module,
            'record_id'   => $recordId,
            'description' => $description,
            'ip_address'  => request()->ip(),
            'user_agent'  => request()->userAgent(),
        ]);
    }
}
