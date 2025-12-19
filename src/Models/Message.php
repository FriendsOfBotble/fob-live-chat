<?php

namespace FriendsOfBotble\LiveChat\Models;

use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends BaseModel
{
    protected $table = 'fob_live_chat_messages';

    protected $fillable = [
        'conversation_id',
        'content',
        'is_from_admin',
        'admin_id',
        'admin_name',
        'is_read',
    ];

    protected function casts(): array
    {
        return [
            'is_from_admin' => 'boolean',
            'is_read' => 'boolean',
        ];
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }
}
