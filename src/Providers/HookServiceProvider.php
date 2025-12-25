<?php

namespace FriendsOfBotble\LiveChat\Providers;

use Botble\Base\Supports\ServiceProvider;
use Botble\Theme\Facades\Theme;
use FriendsOfBotble\LiveChat\Support\LiveChatHelper;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (! LiveChatHelper::isEnabled()) {
            return;
        }

        add_filter(THEME_FRONT_FOOTER, [$this, 'registerAssets'], 25);
    }

    public function registerAssets(?string $html): ?string
    {
        if (is_in_admin()) {
            return $html;
        }

        Theme::asset()
            ->usePath(false)
            ->add(
                'fob-live-chat-css',
                asset('vendor/core/plugins/fob-live-chat/css/live-chat.css'),
                [],
                [],
                '1.0.2'
            );

        Theme::asset()
            ->container('footer')
            ->usePath(false)
            ->add(
                'fob-live-chat-js',
                asset('vendor/core/plugins/fob-live-chat/js/live-chat.js'),
                ['jquery'],
                [],
                '1.0.2'
            );

        return $html . view('plugins/fob-live-chat::widget')->render();
    }
}
