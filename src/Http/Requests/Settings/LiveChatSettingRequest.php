<?php

namespace FriendsOfBotble\LiveChat\Http\Requests\Settings;

use Botble\Support\Http\Requests\Request;

class LiveChatSettingRequest extends Request
{
    public function rules(): array
    {
        return [
            'fob_live_chat_enabled' => ['nullable', 'in:0,1'],
            'fob_live_chat_widget_title' => ['nullable', 'string', 'max:100'],
            'fob_live_chat_welcome_message' => ['nullable', 'string', 'max:500'],
            'fob_live_chat_primary_color' => ['nullable', 'string', 'max:20'],
            'fob_live_chat_position' => ['nullable', 'in:left,right'],
            'fob_live_chat_email_required' => ['nullable', 'in:0,1'],
            'fob_live_chat_display_fields' => ['nullable', 'array'],
            'fob_live_chat_display_fields.*' => ['required', 'in:email,phone'],
            'fob_live_chat_mandatory_fields' => ['nullable', 'array'],
            'fob_live_chat_mandatory_fields.*' => ['required', 'in:email,phone'],
            'fob_live_chat_poll_interval' => ['nullable', 'integer', 'min:1000', 'max:30000'],
        ];
    }
}
