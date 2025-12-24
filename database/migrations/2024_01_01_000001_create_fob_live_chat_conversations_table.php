<?php

use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('fob_live_chat_conversations')) {
            return;
        }

        Schema::create('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id', 100)->index();
            $table->string('visitor_name', 120);
            $table->string('visitor_email', 120)->nullable();
            $table->string('visitor_phone', 25)->nullable();
            $table->string('visitor_ip', 45)->nullable();
            $table->string('current_url', 2048)->nullable();
            $table->text('visitor_user_agent')->nullable();
            $table->string('status', 20)->default(ConversationStatus::OPEN)->index();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fob_live_chat_conversations');
    }
};
