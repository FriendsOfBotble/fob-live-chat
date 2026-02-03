@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="fob-messenger" data-config="{{ json_encode([
        'selectedId' => $selectedId,
        'lastMessageId' => $selected?->messages->last()?->id ?? 0,
        'csrfToken' => csrf_token(),
        'faviconUrl' => asset('vendor/core/core/base/images/favicon.png'),
        'notificationSoundUrl' => asset('vendor/core/plugins/fob-live-chat/sounds/notification.mp3'),
        'routes' => [
            'checkUpdates' => route('fob-live-chat.conversations.check-updates'),
            'show' => route('fob-live-chat.conversations.show', ':id'),
            'messages' => route('fob-live-chat.conversations.messages', ':id'),
        ],
        'translations' => [
            'notificationsEnabled' => trans('plugins/fob-live-chat::live-chat.notifications_enabled'),
            'testSound' => trans('plugins/fob-live-chat::live-chat.test_sound'),
            'newMessage' => trans('plugins/fob-live-chat::live-chat.new_message'),
            'newConversation' => trans('plugins/fob-live-chat::live-chat.new_conversation'),
            'noMessagesYet' => trans('plugins/fob-live-chat::live-chat.no_messages_yet'),
            'justNow' => trans('plugins/fob-live-chat::live-chat.just_now'),
            'confirmClose' => trans('plugins/fob-live-chat::live-chat.confirm_close'),
            'confirmDelete' => trans('plugins/fob-live-chat::live-chat.confirm_delete'),
            'selectConversation' => trans('plugins/fob-live-chat::live-chat.select_conversation'),
            'selectConversationHint' => trans('plugins/fob-live-chat::live-chat.select_conversation_hint'),
        ],
    ]) }}">
        {{-- Conversations List --}}
        <div class="fob-messenger-sidebar">
            <div class="fob-messenger-sidebar-header">
                <div class="fob-sidebar-title">
                    <x-core::icon name="ti ti-messages" class="text-primary" style="width: 22px; height: 22px;" />
                    <h4>{{ trans('plugins/fob-live-chat::live-chat.title') }}</h4>
                    <button type="button" class="btn btn-sm btn-ghost-primary ms-auto" id="enable-notifications-btn" style="display: none;" title="{{ trans('plugins/fob-live-chat::live-chat.enable_notifications') }}">
                        <x-core::icon name="ti ti-bell" />
                    </button>
                    <span class="badge bg-green-lt ms-auto" id="notifications-enabled-badge" style="display: none;">
                        <x-core::icon name="ti ti-bell-ringing" style="width: 14px; height: 14px;" />
                    </span>
                </div>
                @php
                    $openCount = $conversations->filter(fn($c) => $c->status->getValue() === 'open')->count();
                    $closedCount = $conversations->filter(fn($c) => $c->status->getValue() === 'closed')->count();
                @endphp
                <div class="fob-filter-tabs">
                    <button type="button" class="fob-filter-tab active" data-filter="all">
                        <span class="fob-filter-label">{{ trans('plugins/fob-live-chat::live-chat.all') }}</span>
                        <span class="fob-filter-count">{{ $conversations->count() }}</span>
                    </button>
                    <button type="button" class="fob-filter-tab" data-filter="open">
                        <span class="fob-filter-label">{{ trans('plugins/fob-live-chat::live-chat.conversation_statuses.open') }}</span>
                        <span class="fob-filter-count fob-filter-count-open">{{ $openCount }}</span>
                    </button>
                    <button type="button" class="fob-filter-tab" data-filter="closed">
                        <span class="fob-filter-label">{{ trans('plugins/fob-live-chat::live-chat.conversation_statuses.closed') }}</span>
                        <span class="fob-filter-count">{{ $closedCount }}</span>
                    </button>
                </div>
            </div>
            <div class="fob-conversations-list" id="conversations-list">
                @forelse($conversations as $conv)
                    <div class="fob-conversation-item {{ $selectedId == $conv->id ? 'active' : '' }} {{ $conv->unread_count > 0 ? 'has-unread' : '' }}"
                         data-id="{{ $conv->id }}"
                         data-status="{{ $conv->status->getValue() }}">
                        <div class="fob-conversation-avatar {{ $conv->status->getValue() === 'open' ? 'is-online' : '' }}">
                            <span class="fob-avatar-text">{{ strtoupper(substr($conv->visitor_name, 0, 2)) }}</span>
                        </div>
                        <div class="fob-conversation-content">
                            <div class="fob-conversation-header">
                                <span class="fob-conversation-name">{{ $conv->visitor_name }}</span>
                                <span class="fob-conversation-time">{{ $conv->last_message_at ? $conv->last_message_at->diffForHumans(short: true) : '' }}</span>
                            </div>
                            <div class="fob-conversation-body">
                                <span class="fob-conversation-preview {{ !$conv->last_message ? 'text-muted fst-italic' : '' }}">{{ $conv->last_message ? Str::limit($conv->last_message, 50) : trans('plugins/fob-live-chat::live-chat.no_messages_yet') }}</span>
                                @if($conv->unread_count > 0)
                                    <span class="fob-unread-badge">{{ $conv->unread_count > 9 ? '9+' : $conv->unread_count }}</span>
                                @else
                                    <span class="fob-status-dot {{ $conv->status->getValue() === 'open' ? 'is-open' : 'is-closed' }}"></span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="fob-empty-state">
                        <div class="fob-empty-icon">
                            <x-core::icon name="ti ti-messages" style="width: 48px; height: 48px;" />
                        </div>
                        <p class="fob-empty-title">{{ trans('plugins/fob-live-chat::live-chat.no_conversations') }}</p>
                        <p class="fob-empty-desc">{{ trans('plugins/fob-live-chat::live-chat.no_conversations_hint') ?? 'Conversations will appear here when visitors start chatting.' }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Chat Panel --}}
        <div class="fob-messenger-main" id="chat-panel">
            @if($selected)
                @include('plugins/fob-live-chat::admin.conversations.partials.chat-panel', ['conversation' => $selected])
            @else
                <div class="fob-empty-chat">
                    <div class="fob-empty-chat-icon">
                        <x-core::icon name="ti ti-message-circle" style="width: 80px; height: 80px;" />
                    </div>
                    <h3 class="fob-empty-chat-title">{{ trans('plugins/fob-live-chat::live-chat.select_conversation') }}</h3>
                    <p class="fob-empty-chat-desc">{{ trans('plugins/fob-live-chat::live-chat.select_conversation_hint') }}</p>
                </div>
            @endif
        </div>

        {{-- Info Panel --}}
        <div class="fob-messenger-info {{ $selected ? '' : 'd-none' }}" id="info-panel">
            @if($selected)
                @include('plugins/fob-live-chat::admin.conversations.partials.info-panel', ['conversation' => $selected])
            @endif
        </div>
    </div>
@endsection

@push('header')
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/fob-live-chat/css/admin-messenger.css') }}?v={{ FriendsOfBotble\LiveChat\Plugin::VERSION }}">
@endpush

@push('footer')
    <script src="{{ asset('vendor/core/plugins/fob-live-chat/js/admin-messenger.js') }}?v={{ FriendsOfBotble\LiveChat\Plugin::VERSION }}"></script>
@endpush
