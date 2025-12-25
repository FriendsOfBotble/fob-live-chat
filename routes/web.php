<?php

use Botble\Base\Facades\AdminHelper;
use Botble\Theme\Facades\Theme;
use FriendsOfBotble\LiveChat\Http\Controllers\ConversationController;
use FriendsOfBotble\LiveChat\Http\Controllers\Fronts\ChatController;
use FriendsOfBotble\LiveChat\Http\Controllers\Settings\LiveChatSettingController;
use Illuminate\Support\Facades\Route;

Route::name('fob-live-chat.')->group(function (): void {
    AdminHelper::registerRoutes(function (): void {
        Route::group(['prefix' => 'live-chat'], function (): void {
            Route::group(['prefix' => 'conversations', 'as' => 'conversations.'], function (): void {
                Route::get('/', [ConversationController::class, 'index'])
                    ->name('index')
                    ->permission('fob-live-chat.conversations.index');

                Route::get('check-updates', [ConversationController::class, 'checkUpdates'])
                    ->name('check-updates')
                    ->permission('fob-live-chat.conversations.index');

                Route::get('{id}', [ConversationController::class, 'show'])
                    ->name('show')
                    ->permission('fob-live-chat.conversations.show');

                Route::post('{id}/reply', [ConversationController::class, 'reply'])
                    ->name('reply')
                    ->permission('fob-live-chat.conversations.reply');

                Route::post('{id}/close', [ConversationController::class, 'close'])
                    ->name('close')
                    ->permission('fob-live-chat.conversations.reply');

                Route::delete('{id}', [ConversationController::class, 'destroy'])
                    ->name('destroy')
                    ->permission('fob-live-chat.conversations.destroy');

                Route::get('{id}/messages', [ConversationController::class, 'fetchMessages'])
                    ->name('messages')
                    ->permission('fob-live-chat.conversations.show');
            });

            Route::group(['prefix' => 'settings', 'permission' => 'fob-live-chat.settings'], function (): void {
                Route::get('/', [LiveChatSettingController::class, 'edit'])->name('settings');
                Route::put('/', [LiveChatSettingController::class, 'update'])->name('settings.update');
                Route::post('webhook/test', [LiveChatSettingController::class, 'testWebhook'])->name('settings.webhook.test');
            });
        });
    });

    Theme::registerRoutes(function (): void {
        Route::prefix('fob-live-chat')
            ->name('public.')
            ->middleware('throttle:60,1')
            ->group(function (): void {
                Route::post('start', [ChatController::class, 'start'])->name('start');
                Route::get('messages', [ChatController::class, 'messages'])->name('messages');
                Route::post('send', [ChatController::class, 'send'])->name('send');
            });
    });
});
