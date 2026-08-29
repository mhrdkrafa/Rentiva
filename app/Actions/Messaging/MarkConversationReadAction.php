<?php

namespace App\Actions\Messaging;

use App\Models\Conversation;
use App\Models\User;

class MarkConversationReadAction
{
    public function execute(User $user, Conversation $conversation): void
    {
        $conversation->participants()->updateExistingPivot($user->id, [
            'last_read_at' => now(),
        ]);
    }
}
