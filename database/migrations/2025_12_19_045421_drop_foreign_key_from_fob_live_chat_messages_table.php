<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('fob_live_chat_messages')) {
            return;
        }

        $foreignKeys = Schema::getForeignKeys('fob_live_chat_messages');
        $hasForeignKey = collect($foreignKeys)->contains(fn ($key) => in_array('conversation_id', $key['columns']));

        if (! $hasForeignKey) {
            return;
        }

        Schema::table('fob_live_chat_messages', function (Blueprint $table): void {
            $table->dropForeign(['conversation_id']);
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('fob_live_chat_messages')) {
            return;
        }

        $foreignKeys = Schema::getForeignKeys('fob_live_chat_messages');
        $hasForeignKey = collect($foreignKeys)->contains(fn ($key) => in_array('conversation_id', $key['columns']));

        if ($hasForeignKey) {
            return;
        }

        Schema::table('fob_live_chat_messages', function (Blueprint $table): void {
            $table->foreign('conversation_id')
                ->references('id')
                ->on('fob_live_chat_conversations')
                ->cascadeOnDelete();
        });
    }
};
