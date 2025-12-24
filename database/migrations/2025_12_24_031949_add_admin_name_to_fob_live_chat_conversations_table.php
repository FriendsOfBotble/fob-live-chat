<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasColumn('fob_live_chat_conversations', 'admin_name')) {
            return;
        }

        Schema::table('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->string('admin_name', 120)->nullable()->after('current_url');
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('fob_live_chat_conversations', 'admin_name')) {
            return;
        }

        Schema::table('fob_live_chat_conversations', function (Blueprint $table): void {
            $table->dropColumn('admin_name');
        });
    }
};
