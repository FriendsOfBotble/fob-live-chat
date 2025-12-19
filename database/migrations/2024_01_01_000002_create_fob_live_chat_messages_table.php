<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('fob_live_chat_messages')) {
            return;
        }

        Schema::create('fob_live_chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('conversation_id')->index();
            $table->text('content');
            $table->boolean('is_from_admin')->default(false);
            $table->foreignId('admin_id')->nullable()->index();
            $table->string('admin_name', 120)->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fob_live_chat_messages');
    }
};
