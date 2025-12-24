<?php

namespace FriendsOfBotble\LiveChat\Http\Controllers\Fronts;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Helper;
use Carbon\Carbon;
use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use FriendsOfBotble\LiveChat\Events\NewConversationEvent;
use FriendsOfBotble\LiveChat\Http\Requests\Fronts\SendMessageRequest;
use FriendsOfBotble\LiveChat\Http\Requests\Fronts\StartChatRequest;
use FriendsOfBotble\LiveChat\Models\Conversation;
use FriendsOfBotble\LiveChat\Models\Message;
use FriendsOfBotble\LiveChat\Services\WebhookService;
use FriendsOfBotble\LiveChat\Support\LiveChatHelper;
use Illuminate\Http\Request;

class ChatController extends BaseController
{
    public function __construct(protected WebhookService $webhookService)
    {
    }
    public function start(StartChatRequest $request)
    {
        $sessionId = session()->getId();

        $conversation = Conversation::query()
            ->where('session_id', $sessionId)
            ->where('status', ConversationStatus::OPEN)
            ->first();

        $isNewConversation = false;

        if (! $conversation) {
            $conversation = Conversation::query()->create([
                'session_id' => $sessionId,
                'visitor_name' => $request->input('name'),
                'visitor_email' => LiveChatHelper::isEmailEnabled() ? $request->input('email') : null,
                'visitor_phone' => LiveChatHelper::isPhoneEnabled() ? $request->input('phone') : null,
                'visitor_ip' => Helper::getIpFromThirdParty(),
                'visitor_user_agent' => $request->userAgent(),
                'current_url' => $request->input('current_url'),
                'admin_name' => LiveChatHelper::generateRandomAdminName(),
                'status' => ConversationStatus::OPEN,
            ]);

            $isNewConversation = true;
        }

        if ($isNewConversation) {
            $this->webhookService->sendConversationWebhook($conversation, WebhookService::EVENT_CONVERSATION_STARTED);
            event(new NewConversationEvent($conversation));
        }

        return $this
            ->httpResponse()
            ->setData([
                'conversation_id' => $conversation->id,
            ])
            ->setMessage(trans('plugins/fob-live-chat::live-chat.chat_started'));
    }

    public function messages(Request $request)
    {
        $sessionId = session()->getId();
        $after = $request->input('after');

        $conversation = Conversation::query()
            ->where('session_id', $sessionId)
            ->where('status', ConversationStatus::OPEN)
            ->first();

        if (! $conversation) {
            return $this
                ->httpResponse()
                ->setData([
                    'messages' => [],
                    'has_conversation' => false,
                ]);
        }

        $query = $conversation->messages()->orderBy('created_at', 'asc');

        if ($after) {
            try {
                $afterTime = Carbon::parse($after)->setTimezone(config('app.timezone'));
                $query->where('created_at', '>', $afterTime->toDateTimeString());
            } catch (\Exception) {
            }
        }

        $messages = $query->get()->map(fn (Message $message) => [
            'id' => $message->getKey(),
            'content' => $message->content,
            'is_from_admin' => $message->is_from_admin,
            'admin_name' => $message->admin_name,
            'created_at' => $message->created_at->toIso8601String(),
        ]);

        // Mark admin messages as read
        $conversation->messages()
            ->where('is_from_admin', true)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return $this
            ->httpResponse()
            ->setData([
                'messages' => $messages,
                'has_conversation' => true,
            ]);
    }

    public function send(SendMessageRequest $request)
    {
        $sessionId = session()->getId();

        $conversation = Conversation::query()
            ->where('session_id', $sessionId)
            ->where('status', ConversationStatus::OPEN)
            ->first();

        if (! $conversation) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/fob-live-chat::live-chat.no_conversation'));
        }

        $message = Message::query()->create([
            'conversation_id' => $conversation->id,
            'content' => $request->input('message'),
            'is_from_admin' => false,
        ]);

        $conversation->update(['last_message_at' => now()]);

        $this->webhookService->sendMessageWebhook($message, WebhookService::EVENT_MESSAGE_RECEIVED);

        return $this
            ->httpResponse()
            ->setData([
                'message' => [
                    'id' => $message->id,
                    'content' => $message->content,
                    'is_from_admin' => false,
                    'created_at' => $message->created_at->toIso8601String(),
                ],
            ])
            ->setMessage(trans('plugins/fob-live-chat::live-chat.message_sent'));
    }
}
