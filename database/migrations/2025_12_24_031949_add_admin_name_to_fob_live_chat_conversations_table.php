<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('fob_live_chat_conversations', function (Blueprint $table) {
            $table->string('admin_name', 120)->nullable()->after('current_url');
        });
    }

    public function down(): void
    {
        Schema::table('fob_live_chat_conversations', function (Blueprint $table) {
            $table->dropColumn('admin_name');
        });
    }
};
