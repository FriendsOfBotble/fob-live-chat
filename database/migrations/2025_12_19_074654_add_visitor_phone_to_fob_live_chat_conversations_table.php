<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fob_live_chat_conversations', function (Blueprint $table) {
            $table->string('visitor_phone', 25)->nullable()->after('visitor_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fob_live_chat_conversations', function (Blueprint $table) {
            $table->dropColumn('visitor_phone');
        });
    }
};
