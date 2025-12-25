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
            'fob_live_chat_is_online' => ['nullable', 'in:0,1'],
            'fob_live_chat_working_hours_enabled' => ['nullable', 'in:0,1'],
            'fob_live_chat_working_hours_start' => ['nullable', 'date_format:H:i'],
            'fob_live_chat_working_hours_end' => ['nullable', 'date_format:H:i'],
            'fob_live_chat_working_days' => ['nullable', 'array'],
            'fob_live_chat_working_days.*' => ['in:1,2,3,4,5,6,7'],
            'fob_live_chat_avatar_image' => ['nullable', 'string', 'max:500'],
            'fob_live_chat_welcome_message' => ['nullable', 'string', 'max:500'],
            'fob_live_chat_admin_name' => ['nullable', 'string', 'max:120'],
            'fob_live_chat_random_admin_names_enabled' => ['nullable', 'in:0,1'],
            'fob_live_chat_random_admin_names' => ['nullable', 'string', 'max:2000'],
            'fob_live_chat_primary_color' => ['nullable', 'string', 'max:20'],
            'fob_live_chat_primary_hover_color' => ['nullable', 'string', 'max:20'],
            'fob_live_chat_position' => ['nullable', 'in:bottom_right,bottom_left,center_right,center_left'],
            'fob_live_chat_offset_x' => ['nullable', 'integer', 'min:0', 'max:500'],
            'fob_live_chat_offset_y' => ['nullable', 'integer', 'min:0', 'max:500'],
            'fob_live_chat_display_on_mobile' => ['nullable', 'in:always,hide'],
            'fob_live_chat_email_required' => ['nullable', 'in:0,1'],
            'fob_live_chat_display_fields' => ['nullable', 'array'],
            'fob_live_chat_display_fields.*' => ['in:email,phone'],
            'fob_live_chat_mandatory_fields' => ['nullable', 'array'],
            'fob_live_chat_mandatory_fields.*' => ['in:email,phone'],
            'fob_live_chat_poll_interval' => ['nullable', 'integer', 'min:1000', 'max:30000'],
            'fob_live_chat_enable_email_notification' => ['nullable', 'in:0,1'],
            'fob_live_chat_notification_emails' => ['nullable', 'string', 'max:500'],
            'fob_live_chat_enable_webhooks' => ['nullable', 'in:0,1'],
            'fob_live_chat_message_received_webhook_url' => ['nullable', 'url', 'max:500'],
            'fob_live_chat_conversation_started_webhook_url' => ['nullable', 'url', 'max:500'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fob_live_chat_message_received_webhook_url' => trans('plugins/fob-live-chat::live-chat.webhook.message_received_url'),
            'fob_live_chat_conversation_started_webhook_url' => trans('plugins/fob-live-chat::live-chat.webhook.conversation_started_url'),
        ];
    }
}
