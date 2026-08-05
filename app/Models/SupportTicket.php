<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_number', 'subject', 'description', 'status', 'priority', 'category',
        'requester_admin_id', 'requester_name', 'requester_email',
        'assigned_to', 'created_by', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket) {
            $ticket->ticket_number ??= 'TCK-'.str_pad((string) (static::max('id') + 1), 6, '0', STR_PAD_LEFT);
        });
    }

    public function requester(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'requester_admin_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(SupportTicketReply::class, 'ticket_id')->orderBy('created_at');
    }

    public function requesterName(): string
    {
        return $this->requester->name ?? $this->requester_name ?? 'Unknown';
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'open'        => 'primary',
            'in_progress' => 'warning',
            'resolved'    => 'success',
            'closed'      => 'secondary',
            default       => 'light',
        };
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'urgent' => 'danger',
            'high'   => 'warning',
            'medium' => 'info',
            'low'    => 'secondary',
            default  => 'light',
        };
    }
}
