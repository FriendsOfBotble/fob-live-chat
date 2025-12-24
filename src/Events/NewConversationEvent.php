<?php

namespace FriendsOfBotble\LiveChat\Events;

use FriendsOfBotble\LiveChat\Models\Conversation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewConversationEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Conversation $conversation)
    {
    }
}
