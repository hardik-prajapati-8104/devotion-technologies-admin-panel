<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'conversation_id', 'admin_id', 'body', 'attachment', 'type',
        'reply_to_id', 'forwarded_from_id', 'edited_at', 'pinned_at', 'pinned_by',
        'attachment_name', 'attachment_mime', 'attachment_size', 'meta',
    ];

    protected $casts = [
        'edited_at' => 'datetime',
        'pinned_at' => 'datetime',
        'meta' => 'array',
    ];

    // 3. Type-check helpers, used throughout the Blade views and JS payloads:

    public function isDocument(): bool { return $this->type === 'document'; }
    public function isImage(): bool    { return $this->type === 'image'; }
    public function isVideo(): bool    { return $this->type === 'video'; }
    public function isAudio(): bool    { return $this->type === 'audio'; }
    public function isContact(): bool  { return $this->type === 'contact'; }
    public function isPoll(): bool     { return $this->type === 'poll'; }
    public function isEvent(): bool    { return $this->type === 'event'; }

    /**
     * Human-readable file size for document/media bubbles, e.g. "2.4 MB".
     */
    public function humanFileSize(): ?string
    {
        if (! $this->attachment_size) {
            return null;
        }

        $bytes = $this->attachment_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $bytes < 10 ? 1 : 0) . ' ' . $units[$i];
    }

    /**
     * Public URL for the stored attachment (documents, images, video, audio).
     *
     * Uses asset('storage/...'), which resolves through the public/storage
     * symlink created by `php artisan storage:link`. url('storage/app/public/...')
     * is NOT a valid public URL — that's a filesystem path, not a web path —
     * so attachments would 404 even though the file exists on disk.
     */
    public function attachmentUrl(): ?string
    {
        return $this->attachment ? asset('storage/app/public/' . $this->attachment) : null;
    }

    /**
     * Tally poll votes into per-option counts + percentages, and tell the
     * caller whether $forAdminId has already voted (and on what).
     *
     * meta shape:
     * [
     *   'question' => string,
     *   'options'  => [['id' => 'opt_1', 'text' => 'Yes'], ...],
     *   'multiple' => bool,
     *   'votes'    => ['<admin_id>' => 'opt_1' | ['opt_1','opt_2'] ],
     * ]
     */
    public function pollResults(?int $forAdminId = null): array
    {
        $meta = $this->meta ?? [];
        $options = $meta['options'] ?? [];
        $votes = $meta['votes'] ?? [];
        $multiple = ! empty($meta['multiple']);

        // Flatten every cast vote into a single list of option ids.
        $allVotes = [];
        foreach ($votes as $adminId => $v) {
            foreach ((array) $v as $optId) {
                $allVotes[] = $optId;
            }
        }

        $totalVoters = count($votes);

        $results = collect($options)->map(function ($opt) use ($allVotes, $totalVoters) {
            $count = count(array_filter($allVotes, fn ($id) => $id === $opt['id']));
            return [
                'id'         => $opt['id'],
                'text'       => $opt['text'],
                'count'      => $count,
                'percentage' => $totalVoters > 0 ? round(($count / $totalVoters) * 100) : 0,
            ];
        })->values()->all();

        $myVote = $forAdminId !== null ? ($votes[$forAdminId] ?? null) : null;

        return [
            'question'     => $meta['question'] ?? '',
            'multiple'     => $multiple,
            'options'      => $results,
            'total_voters' => $totalVoters,
            'my_vote'      => $myVote, // string option id, array of ids, or null
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(ChatConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_id')->withTrashed();
    }

    public function forwardedFrom(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'forwarded_from_id')->withTrashed();
    }

    public function pinnedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'pinned_by');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(ChatMessageReaction::class, 'message_id');
    }

    public function stars(): HasMany
    {
        return $this->hasMany(ChatMessageStar::class, 'message_id');
    }

    public function isStarredBy(Admin $admin): bool
    {
        return $this->stars->contains('admin_id', $admin->id);
    }

    /**
     * Groups reactions by emoji for display: ['👍' => 3, '❤️' => 1, ...]
     */
    public function reactionSummary(): array
    {
        return $this->reactions
            ->groupBy('emoji')
            ->map->count()
            ->toArray();
    }

    public function myReaction(Admin $admin): ?string
    {
        return $this->reactions->firstWhere('admin_id', $admin->id)?->emoji;
    }

    /**
     * "Read" for a 1:1 chat = the other participant's last_read_message_id
     * is at or past this message. (Group chats show "read" once every
     * other participant has caught up — kept simple/conservative.)
     */
    public function isReadByOthers(): bool
    {
        return \DB::table('chat_conversation_participants')
            ->where('conversation_id', $this->conversation_id)
            ->where('admin_id', '!=', $this->admin_id)
            ->where('last_read_message_id', '>=', $this->id)
            ->exists();
    }
}