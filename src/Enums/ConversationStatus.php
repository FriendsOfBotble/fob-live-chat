<?php

namespace FriendsOfBotble\LiveChat\Enums;

use Botble\Base\Supports\Enum;

class ConversationStatus extends Enum
{
    public const OPEN = 'open';

    public const CLOSED = 'closed';

    protected static $langPath = 'plugins/fob-live-chat::live-chat.conversation_statuses';

    public function toHtml(): string
    {
        $color = match ($this->value) {
            self::OPEN => 'success',
            self::CLOSED => 'secondary',
            default => 'secondary',
        };

        return sprintf('<span class="badge bg-%s text-white">%s</span>', $color, $this->label());
    }
}
