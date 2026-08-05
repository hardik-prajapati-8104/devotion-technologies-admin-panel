<?php
// routes/channels.php — add this alongside your existing channel definitions

use App\Models\ChatConversation;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.conversation.{conversationId}', function ($user, $conversationId) {
    // $user here is resolved via the 'admin' guard (see broadcasting.php note below).
    return ChatConversation::find($conversationId)
        ?->participants()
        ->where('admin_id', $user->id)
        ->exists() ?? false;
});
