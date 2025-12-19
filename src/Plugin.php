<?php

namespace FriendsOfBotble\LiveChat;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;
use Illuminate\Support\Facades\Schema;

class Plugin extends PluginOperationAbstract
{
    public static function remove(): void
    {
        Schema::dropIfExists('fob_live_chat_messages');
        Schema::dropIfExists('fob_live_chat_conversations');

        Setting::query()
            ->where('key', 'like', 'fob_live_chat_%')
            ->delete();
    }
}
