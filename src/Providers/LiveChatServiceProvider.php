<?php

namespace FriendsOfBotble\LiveChat\Providers;

use Botble\Base\Facades\DashboardMenu;
use Botble\Base\Facades\EmailHandler;
use Botble\Base\Facades\PanelSectionManager;
use Botble\Base\PanelSections\PanelSectionItem;
use Botble\Base\Supports\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;
use Botble\Setting\PanelSections\SettingOthersPanelSection;
use FriendsOfBotble\LiveChat\Enums\ConversationStatus;
use FriendsOfBotble\LiveChat\Events\NewConversationEvent;
use FriendsOfBotble\LiveChat\Listeners\SendNewConversationEmailListener;
use FriendsOfBotble\LiveChat\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

class LiveChatServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this->loadHelpers();

        $this
            ->setNamespace('plugins/fob-live-chat')
            ->publishAssets()
            ->loadAndPublishViews()
            ->loadRoutes()
            ->loadAndPublishConfigurations(['permissions', 'email'])
            ->loadAndPublishTranslations()
            ->loadMigrations();

        Event::listen(NewConversationEvent::class, SendNewConversationEmailListener::class);

        DashboardMenu::default()->beforeRetrieving(function (): void {
            DashboardMenu::make()
                ->registerItem([
                    'id' => 'cms-plugins-fob-live-chat',
                    'priority' => 100,
                    'name' => 'plugins/fob-live-chat::live-chat.title',
                    'icon' => 'ti ti-message-circle',
                    'route' => 'fob-live-chat.conversations.index',
                ]);
        });

        PanelSectionManager::default()->beforeRendering(function (): void {
            PanelSectionManager::registerItem(
                SettingOthersPanelSection::class,
                fn () => PanelSectionItem::make('fob-live-chat')
                    ->setTitle(trans('plugins/fob-live-chat::live-chat.settings.title'))
                    ->withDescription(trans('plugins/fob-live-chat::live-chat.settings.description'))
                    ->withIcon('ti ti-message-cog')
                    ->withPriority(0)
                    ->withRoute('fob-live-chat.settings')
            );
        });

        $this->app->booted(function (): void {
            EmailHandler::addTemplateSettings(FOB_LIVE_CHAT_MODULE_SCREEN_NAME, config('plugins.fob-live-chat.email', []));

            add_filter(BASE_FILTER_APPEND_MENU_NAME, function (?string $html, string $menuId) {
                if ($menuId !== 'cms-plugins-fob-live-chat') {
                    return $html;
                }

                return view('core/base::partials.navbar.badge-count', ['class' => 'unread-conversations-count']);
            }, 1, 2);

            add_filter(BASE_FILTER_MENU_ITEMS_COUNT, function (array $data = []) {
                if (! Auth::guard()->user()->hasPermission('fob-live-chat.conversations.index')) {
                    return $data;
                }

                $data[] = [
                    'key' => 'unread-conversations-count',
                    'value' => Conversation::query()->where('status', ConversationStatus::OPEN)->count(),
                ];

                return $data;
            }, 1, 2);

            $this->app->register(HookServiceProvider::class);
        });
    }
}
