<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class ChatConversation extends Model
{
    protected $fillable = ['type', 'name', 'created_by', 'last_message_at'];

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
            ->withPivot('last_read_message_id', 'joined_at')
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

    /**
     * Raw DB read, deliberately not going through the participants
     * relation/pivot collection — this must always reflect the exact
     * current row, never a cached or eager-loaded snapshot from earlier
     * in the request.
     */
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
}