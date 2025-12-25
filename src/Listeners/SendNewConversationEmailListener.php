<?php

namespace FriendsOfBotble\LiveChat\Listeners;

use Botble\Base\Facades\EmailHandler;
use FriendsOfBotble\LiveChat\Events\NewConversationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendNewConversationEmailListener implements ShouldQueue
{
    public function handle(NewConversationEvent $event): void
    {
        $conversation = $event->conversation;

        if (! setting('fob_live_chat_enable_email_notification', true)) {
            return;
        }

        $receiverEmails = $this->getReceiverEmails();

        if (empty($receiverEmails)) {
            return;
        }

        $conversationUrl = route('fob-live-chat.conversations.show', $conversation->id);

        try {
            EmailHandler::setModule(FOB_LIVE_CHAT_MODULE_SCREEN_NAME)
                ->setVariableValues([
                    'visitor_name' => $conversation->visitor_name,
                    'visitor_email' => $conversation->visitor_email ?: '-',
                    'visitor_phone' => $conversation->visitor_phone ?: '-',
                    'visitor_ip' => $conversation->visitor_ip ?: '-',
                    'current_page' => $conversation->current_url ?: '-',
                    'conversation_url' => $conversationUrl,
                ])
                ->sendUsingTemplate('new-conversation', $receiverEmails);
        } catch (Throwable $e) {
            Log::error('FOB Live Chat: Failed to send email notification - ' . $e->getMessage());
        }
    }

    protected function getReceiverEmails(): string|array|null
    {
        $receiverEmails = setting('fob_live_chat_notification_emails', '');

        if (empty($receiverEmails)) {
            return setting('admin_email');
        }

        $receiverEmails = trim($receiverEmails);

        if (str_contains($receiverEmails, '[')) {
            $receiverEmails = collect(json_decode($receiverEmails, true))
                ->pluck('value')
                ->all();
        } else {
            $receiverEmails = array_map('trim', explode(',', $receiverEmails));
        }

        $receiverEmails = array_filter($receiverEmails);

        if (count($receiverEmails) === 1) {
            return Arr::first($receiverEmails);
        }

        return $receiverEmails ?: null;
    }
}
