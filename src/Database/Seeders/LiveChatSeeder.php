<?php

namespace FriendsOfBotble\LiveChat\Database\Seeders;

use Botble\ACL\Models\User;
use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use FriendsOfBotble\LiveChat\Models\Conversation;
use FriendsOfBotble\LiveChat\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class LiveChatSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(LiveChatConversationSeeder::class);
    }
}

class LiveChatConversationSeeder extends Seeder
{
    protected array $visitorNames = [
        'John Smith',
        'Emily Johnson',
        'Michael Brown',
        'Sarah Davis',
        'James Wilson',
        'Jessica Martinez',
        'David Anderson',
        'Ashley Taylor',
        'Robert Thomas',
        'Amanda White',
    ];

    protected array $browsers = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15',
        'Mozilla/5.0 (iPhone; CPU iPhone OS 17_2 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Mobile/15E148 Safari/604.1',
        'Mozilla/5.0 (Linux; Android 14; SM-S918B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.6099.144 Mobile Safari/537.36',
    ];

    protected array $pages = [
        '/',
        '/products',
        '/products/sample-product',
        '/about-us',
        '/contact',
        '/pricing',
        '/blog',
        '/faq',
        '/services',
        '/cart',
    ];

    protected array $visitorMessages = [
        'Hello, I have a question about your products.',
        'Hi there! Can you help me?',
        'I need assistance with my order.',
        'What are your shipping options?',
        'Do you offer international shipping?',
        'How long does delivery take?',
        'Can I return a product if I\'m not satisfied?',
        'What payment methods do you accept?',
        'Is this product available in other colors?',
        'Can you tell me more about your warranty?',
        'I\'m having trouble checking out.',
        'The discount code is not working.',
        'When will this item be back in stock?',
        'Do you have a physical store?',
        'Can I change my shipping address?',
        'Thank you for your help!',
        'That makes sense, thanks!',
        'Perfect, I\'ll proceed with the order.',
        'One more question...',
        'Great, that answers my question.',
    ];

    protected array $adminResponses = [
        'Hello! Welcome to our store. How can I assist you today?',
        'Hi! I\'d be happy to help you with that.',
        'Thank you for reaching out! Let me check that for you.',
        'We offer free shipping on orders over $50.',
        'Yes, we ship to most countries worldwide.',
        'Standard delivery takes 3-5 business days.',
        'Absolutely! We have a 30-day return policy.',
        'We accept Visa, MasterCard, PayPal, and Apple Pay.',
        'Let me check our inventory for other colors.',
        'All our products come with a 1-year warranty.',
        'I\'m sorry to hear that. Let me help you troubleshoot.',
        'Could you please try the code again? Make sure there are no spaces.',
        'We expect it to be back in stock next week.',
        'We\'re an online-only store, but we offer fast shipping!',
        'I can update that for you. What\'s the new address?',
        'You\'re welcome! Is there anything else I can help with?',
        'Feel free to ask!',
        'Great choice! Let me know if you need anything else.',
        'Sure, go ahead!',
        'Happy to help! Have a great day!',
    ];

    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Message::query()->truncate();
        Conversation::query()->truncate();
        Schema::enableForeignKeyConstraints();

        $admin = User::query()->first();
        $baseUrl = config('app.url', 'https://example.com');

        $conversations = $this->generateConversations($baseUrl);

        foreach ($conversations as $convData) {
            $conversation = Conversation::query()->create([
                'session_id' => $convData['session_id'],
                'visitor_name' => $convData['visitor_name'],
                'visitor_email' => $convData['visitor_email'],
                'visitor_ip' => $convData['visitor_ip'],
                'visitor_user_agent' => $convData['visitor_user_agent'],
                'current_url' => $convData['current_url'],
                'status' => $convData['status'],
                'last_message_at' => $convData['last_message_at'],
                'created_at' => $convData['created_at'],
                'updated_at' => $convData['updated_at'],
            ]);

            $this->generateMessages($conversation, $admin, $convData['created_at']);
        }

        $this->command->info('Created ' . count($conversations) . ' conversations with messages.');
    }

    protected function generateConversations(string $baseUrl): array
    {
        $conversations = [];
        $now = Carbon::now();

        for ($i = 0; $i < 10; $i++) {
            $createdAt = $now->copy()->subDays(rand(0, 14))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
            $visitorName = $this->visitorNames[$i];
            $hasEmail = rand(0, 1);

            $conversations[] = [
                'session_id' => Str::uuid()->toString(),
                'visitor_name' => $visitorName,
                'visitor_email' => $hasEmail ? Str::slug($visitorName) . '@example.com' : null,
                'visitor_ip' => $this->generateRandomIp(),
                'visitor_user_agent' => $this->browsers[array_rand($this->browsers)],
                'current_url' => $baseUrl . $this->pages[array_rand($this->pages)],
                'status' => $i < 4 ? ConversationStatus::OPEN : ConversationStatus::CLOSED,
                'last_message_at' => $createdAt->copy()->addMinutes(rand(5, 120)),
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];
        }

        usort($conversations, fn ($a, $b) => $b['created_at'] <=> $a['created_at']);

        return $conversations;
    }

    protected function generateMessages(Conversation $conversation, ?User $admin, Carbon $startTime): void
    {
        $messageCount = rand(3, 8);
        $currentTime = $startTime->copy();
        $usedVisitorMessages = [];
        $usedAdminResponses = [];

        for ($i = 0; $i < $messageCount; $i++) {
            $isFromAdmin = $i > 0 && $i % 2 === 1;
            $currentTime = $currentTime->copy()->addMinutes(rand(1, 15));

            if ($isFromAdmin) {
                $content = $this->getUniqueMessage($this->adminResponses, $usedAdminResponses);
            } else {
                $content = $this->getUniqueMessage($this->visitorMessages, $usedVisitorMessages);
            }

            Message::query()->create([
                'conversation_id' => $conversation->id,
                'content' => $content,
                'is_from_admin' => $isFromAdmin,
                'admin_id' => $isFromAdmin && $admin ? $admin->id : null,
                'admin_name' => $isFromAdmin && $admin ? $admin->name : null,
                'is_read' => $isFromAdmin || $conversation->status === ConversationStatus::CLOSED,
                'created_at' => $currentTime,
                'updated_at' => $currentTime,
            ]);
        }

        $conversation->update(['last_message_at' => $currentTime]);
    }

    protected function getUniqueMessage(array $messages, array &$used): string
    {
        $available = array_diff($messages, $used);

        if (empty($available)) {
            $used = [];
            $available = $messages;
        }

        $message = $available[array_rand($available)];
        $used[] = $message;

        return $message;
    }

    protected function generateRandomIp(): string
    {
        return rand(1, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }
}
