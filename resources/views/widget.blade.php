@php
    $config = [
        'startUrl' => route('fob-live-chat.public.start'),
        'messagesUrl' => route('fob-live-chat.public.messages'),
        'sendUrl' => route('fob-live-chat.public.send'),
        'pollInterval' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getPollInterval(),
        'position' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getPosition(),
        'offsetX' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getOffsetX(),
        'offsetY' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getOffsetY(),
        'hiddenOnMobile' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::isHiddenOnMobile(),
        'primaryColor' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getPrimaryColor(),
        'primaryHoverColor' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getPrimaryHoverColor(),
        'widgetTitle' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getWidgetTitle(),
        'statusText' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getCurrentStatusText(),
        'statusColor' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getCurrentStatusColor(),
        'avatarImage' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getAvatarImage(),
        'csrfToken' => csrf_token(),
        'welcomeMessage' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::getWelcomeMessage(),
        'emailEnabled' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::isEmailEnabled(),
        'emailRequired' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::isEmailRequired(),
        'phoneEnabled' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::isPhoneEnabled(),
        'phoneRequired' => FriendsOfBotble\LiveChat\Support\LiveChatHelper::isPhoneRequired(),
    ];
@endphp

{{-- Embed CSS directly --}}
<link rel="stylesheet" href="{{ asset('vendor/core/plugins/fob-live-chat/css/live-chat.css') }}?v=1.0.1">

<div id="fob-live-chat" class="fob-live-chat fob-position-{{ $config['position'] }}{{ $config['hiddenOnMobile'] ? ' fob-hidden-on-mobile' : '' }}" data-config='@json($config)' style="--fob-primary: {{ $config['primaryColor'] }}; --fob-primary-hover: {{ $config['primaryHoverColor'] }}; --fob-status-color: {{ $config['statusColor'] }}; --fob-offset-x: {{ $config['offsetX'] }}px; --fob-offset-y: {{ $config['offsetY'] }}px;">

    {{-- Chat Button --}}
    <button type="button" class="fob-chat-button" aria-label="{{ trans('plugins/fob-live-chat::live-chat.open_chat') }}">
        <svg class="fob-icon-chat" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
        </svg>
        <svg class="fob-icon-close" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg>
        <span class="fob-chat-badge" style="display:none;">0</span>
    </button>

    {{-- Chat Window --}}
    <div class="fob-chat-window" data-status="closed">
        {{-- Header --}}
        <div class="fob-chat-header">
            <div class="fob-chat-header-info">
                <div class="fob-chat-avatar">
                    @if($config['avatarImage'])
                        <img src="{{ RvMedia::getImageUrl($config['avatarImage']) }}" alt="{{ $config['widgetTitle'] }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    @endif
                </div>
                <div class="fob-chat-header-text">
                    <span class="fob-chat-title">{{ $config['widgetTitle'] }}</span>
                    <span class="fob-chat-status">{{ $config['statusText'] }}</span>
                </div>
            </div>
            <button type="button" class="fob-chat-close" aria-label="{{ trans('plugins/fob-live-chat::live-chat.close') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        {{-- Start Form (shown when no conversation) --}}
        <div class="fob-chat-start">
            <div class="fob-chat-start-content">
                <div class="fob-chat-start-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                </div>
                <p class="fob-chat-start-text">{{ trans('plugins/fob-live-chat::live-chat.start_message') }}</p>
                <form class="fob-chat-start-form">
                    <div class="fob-form-group">
                        <input type="text" name="name" class="fob-input" placeholder="{{ trans('plugins/fob-live-chat::live-chat.your_name') }}" required>
                    </div>
                    @if($config['emailEnabled'])
                        <div class="fob-form-group">
                            <input type="email" name="email" class="fob-input" placeholder="{{ $config['emailRequired'] ? trans('plugins/fob-live-chat::live-chat.your_email') : trans('plugins/fob-live-chat::live-chat.your_email_optional') }}" @if($config['emailRequired']) required @endif>
                        </div>
                    @endif
                    @if($config['phoneEnabled'])
                        <div class="fob-form-group">
                            <input type="tel" name="phone" class="fob-input" placeholder="{{ trans('plugins/fob-live-chat::live-chat.your_phone') }}" @if($config['phoneRequired']) required @endif>
                        </div>
                    @endif
                    <button type="submit" class="fob-btn fob-btn-primary">
                        <span>{{ trans('plugins/fob-live-chat::live-chat.start_chat') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Messages Container (hidden initially) --}}
        <div class="fob-chat-body" style="display:none;">
            <div class="fob-chat-messages"></div>
        </div>

        {{-- Input Area (hidden initially) --}}
        <div class="fob-chat-footer" style="display:none;">
            <form class="fob-chat-send-form">
                <input type="text" name="message" class="fob-input" placeholder="{{ trans('plugins/fob-live-chat::live-chat.type_message') }}" maxlength="2000" required autocomplete="off">
                <button type="submit" class="fob-btn-send" aria-label="{{ trans('plugins/fob-live-chat::live-chat.send') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"></line>
                        <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                    </svg>
                </button>
            </form>
        </div>

        {{-- Powered By --}}
        <div class="fob-chat-powered">
            <span>Powered by</span>
            <a href="https://friendsofbotble.com" target="_blank" rel="noopener">FOB Live Chat</a>
        </div>
    </div>
</div>

{{-- Embed JS directly --}}
<script src="{{ asset('vendor/core/plugins/fob-live-chat/js/live-chat.js') }}?v=1.0.1"></script>
