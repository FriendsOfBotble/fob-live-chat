<?php

namespace FriendsOfBotble\LiveChat\Services;

use FriendsOfBotble\LiveChat\Models\Conversation;
use FriendsOfBotble\LiveChat\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class WebhookService
{
    public const EVENT_MESSAGE_RECEIVED = 'message.received';

    public const EVENT_CONVERSATION_STARTED = 'conversation.started';

    public function getWebhookUrl(string $event): ?string
    {
        return match ($event) {
            self::EVENT_MESSAGE_RECEIVED => setting('fob_live_chat_message_received_webhook_url'),
            self::EVENT_CONVERSATION_STARTED => setting('fob_live_chat_conversation_started_webhook_url'),
            default => null,
        };
    }

    public function isEnabled(): bool
    {
        return (bool) setting('fob_live_chat_enable_webhooks', false);
    }

    public function sendMessageWebhook(Message $message, string $event): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $url = $this->getWebhookUrl($event);

        if (! $url) {
            return false;
        }

        $payload = $this->buildMessagePayload($message, $event);

        return $this->send($url, $payload);
    }

    public function sendConversationWebhook(Conversation $conversation, string $event): bool
    {
        if (! $this->isEnabled()) {
            return false;
        }

        $url = $this->getWebhookUrl($event);

        if (! $url) {
            return false;
        }

        $payload = $this->buildConversationPayload($conversation, $event);

        return $this->send($url, $payload);
    }

    public function buildMessagePayload(Message $message, string $event): array
    {
        $message->load('conversation');
        $conversation = $message->conversation;

        return [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'message_id' => $message->id,
                'content' => $message->content,
                'is_from_admin' => $message->is_from_admin,
                'admin_name' => $message->admin_name,
                'conversation_id' => $conversation->id,
                'visitor_name' => $conversation->visitor_name,
                'visitor_email' => $conversation->visitor_email,
                'visitor_phone' => $conversation->visitor_phone,
                'visitor_ip' => $conversation->visitor_ip,
                'current_url' => $conversation->current_url,
                'status' => $conversation->status->getValue(),
                'created_at' => $message->created_at?->toIso8601String(),
            ],
        ];
    }

    public function buildConversationPayload(Conversation $conversation, string $event): array
    {
        return [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'conversation_id' => $conversation->id,
                'visitor_name' => $conversation->visitor_name,
                'visitor_email' => $conversation->visitor_email,
                'visitor_phone' => $conversation->visitor_phone,
                'visitor_ip' => $conversation->visitor_ip,
                'current_url' => $conversation->current_url,
                'status' => $conversation->status->getValue(),
                'created_at' => $conversation->created_at?->toIso8601String(),
            ],
        ];
    }

    public function send(string $url, array $payload): bool
    {
        try {
            $headers = [
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $payload['event'],
                'X-Webhook-Timestamp' => $payload['timestamp'],
            ];

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $payload);

            if ($response->failed()) {
                Log::warning('Live Chat webhook failed', [
                    'url' => $url,
                    'event' => $payload['event'],
                    'status' => $response->status(),
                    'response' => $response->body(),
                ]);

                return false;
            }

            Log::info('Live Chat webhook sent successfully', [
                'url' => $url,
                'event' => $payload['event'],
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error('Live Chat webhook error', [
                'url' => $url,
                'event' => $payload['event'],
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function testWebhook(string $url, string $webhookType): array
    {
        $event = match ($webhookType) {
            'message_received' => self::EVENT_MESSAGE_RECEIVED,
            'conversation_started' => self::EVENT_CONVERSATION_STARTED,
            default => self::EVENT_MESSAGE_RECEIVED,
        };

        $payload = $this->getTestPayload($event);

        try {
            $headers = [
                'Content-Type' => 'application/json',
                'X-Webhook-Event' => $payload['event'],
                'X-Webhook-Timestamp' => $payload['timestamp'],
                'X-Webhook-Test' => 'true',
            ];

            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->post($url, $payload);

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'message' => $response->successful()
                    ? trans('plugins/fob-live-chat::live-chat.webhook.test_success')
                    : trans('plugins/fob-live-chat::live-chat.webhook.test_failed'),
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'status_code' => 0,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function getTestPayload(string $event): array
    {
        if ($event === self::EVENT_CONVERSATION_STARTED) {
            return [
                'event' => $event,
                'timestamp' => now()->toIso8601String(),
                'data' => [
                    'conversation_id' => 123,
                    'visitor_name' => 'Test Visitor',
                    'visitor_email' => 'test@example.com',
                    'visitor_phone' => '+1234567890',
                    'visitor_ip' => '127.0.0.1',
                    'current_url' => 'https://example.com/products',
                    'status' => 'open',
                    'created_at' => now()->toIso8601String(),
                ],
            ];
        }

        return [
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'message_id' => 456,
                'content' => 'This is a test message from the webhook system.',
                'is_from_admin' => false,
                'admin_name' => null,
                'conversation_id' => 123,
                'visitor_name' => 'Test Visitor',
                'visitor_email' => 'test@example.com',
                'visitor_phone' => '+1234567890',
                'visitor_ip' => '127.0.0.1',
                'current_url' => 'https://example.com/products',
                'status' => 'open',
                'created_at' => now()->toIso8601String(),
            ],
        ];
    }
}
