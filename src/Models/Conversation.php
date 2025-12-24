<?php

namespace FriendsOfBotble\LiveChat\Models;

use Botble\Base\Models\BaseModel;
use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends BaseModel
{
    protected $table = 'fob_live_chat_conversations';

    protected $fillable = [
        'session_id',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'visitor_ip',
        'visitor_user_agent',
        'current_url',
        'admin_name',
        'status',
        'last_message_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ConversationStatus::class,
            'last_message_at' => 'datetime',
        ];
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'conversation_id')->latestOfMany();
    }

    public function unreadMessagesCount(): int
    {
        return $this->messages()->where('is_from_admin', false)->where('is_read', false)->count();
    }
}
