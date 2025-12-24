<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('fob_live_chat_conversations', 'current_url')) {
            return;
        }

        Schema::table('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->string('current_url', 2048)->nullable()->after('visitor_ip');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('fob_live_chat_conversations', 'current_url')) {
            return;
        }

        Schema::table('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->dropColumn('current_url');
        });
    }
};
