<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('fob_live_chat_messages', function (Blueprint $table): void {
            $table->dropForeign(['conversation_id']);
        });
    }

    public function down(): void
    {
        Schema::table('fob_live_chat_messages', function (Blueprint $table): void {
            $table->foreign('conversation_id')
                ->references('id')
                ->on('fob_live_chat_conversations')
                ->cascadeOnDelete();
        });
    }
};
