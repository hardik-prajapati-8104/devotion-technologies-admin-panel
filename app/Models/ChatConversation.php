<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ChatConversation extends Model
{
    protected $fillable = ['type', 'name', 'avatar', 'description', 'created_by', 'last_message_at'];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(
            Admin::class,
            'chat_conversation_participants',
            'conversation_id',
            'admin_id'
        )
            ->withPivot('last_read_message_id', 'joined_at', 'is_admin', 'cleared_at')
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id')->orderBy('created_at');
    }

    public function latestMessage()
    {
        return $this->hasOne(ChatMessage::class, 'conversation_id')->latestOfMany();
    }

    public function displayNameFor(Admin $viewer): string
    {
        if ($this->type === 'group') {
            return $this->name ?? 'Group Chat';
        }

        $other = $this->participants->firstWhere('id', '!=', $viewer->id);
        return $other?->name ?? 'Direct Message';
    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->avatar ? Storage::disk('public')->url($this->avatar) : null;
    }

    public function unreadCountFor(Admin $admin): int
    {
        $lastReadId = DB::table('chat_conversation_participants')
            ->where('conversation_id', $this->id)
            ->where('admin_id', $admin->id)
            ->value('last_read_message_id') ?? 0;

        return DB::table('chat_messages')
            ->where('conversation_id', $this->id)
            ->where('id', '>', $lastReadId)
            ->where('admin_id', '!=', $admin->id)
            ->count();
    }

    public function isAdmin(Admin $admin): bool
    {
        if ($this->created_by === $admin->id) {
            return true;
        }

        return DB::table('chat_conversation_participants')
            ->where('conversation_id', $this->id)
            ->where('admin_id', $admin->id)
            ->where('is_admin', true)
            ->exists();
    }

    /**
     * Messages visible to THIS admin — i.e. sent after their own
     * cleared_at, if they've ever cleared the chat. Other participants
     * are unaffected; "Clear Chat" only hides history for the person
     * who cleared it, it never deletes anything.
     */
    public function visibleMessagesFor(Admin $admin)
    {
        $clearedAt = DB::table('chat_conversation_participants')
            ->where('conversation_id', $this->id)
            ->where('admin_id', $admin->id)
            ->value('cleared_at');

        $query = $this->messages()->with('sender');

        if ($clearedAt) {
            $query->where('created_at', '>', $clearedAt);
        }

        return $query;
    }
}