<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('fob_live_chat_conversations', 'visitor_phone')) {
            return;
        }

        Schema::table('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->string('visitor_phone', 25)->nullable()->after('visitor_email');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('fob_live_chat_conversations', 'visitor_phone')) {
            return;
        }

        Schema::table('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->dropColumn('visitor_phone');
        });
    }
};
