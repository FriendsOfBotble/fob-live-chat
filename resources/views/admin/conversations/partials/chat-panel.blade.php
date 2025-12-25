{{-- Chat Header --}}
<div class="fob-chat-header">
    <div class="fob-chat-header-info">
        <div class="fob-conversation-avatar {{ $conversation->status->getValue() === 'open' ? 'is-online' : '' }}">
            <span class="fob-avatar-text">{{ strtoupper(substr($conversation->visitor_name, 0, 2)) }}</span>
        </div>
        <div class="fob-chat-header-details">
            <div class="fob-chat-header-name">{{ $conversation->visitor_name }}</div>
            @if($conversation->visitor_email)
                <div class="fob-chat-header-email">{{ $conversation->visitor_email }}</div>
            @else
                <div class="fob-chat-header-email">
                    <span class="badge {{ $conversation->status->getValue() === 'open' ? 'bg-green text-white' : 'bg-secondary text-white' }}">
                        {{ $conversation->status->label() }}
                    </span>
                </div>
            @endif
        </div>
    </div>
    <div class="fob-chat-header-actions">
        @if($conversation->status->getValue() === \FriendsOfBotble\LiveChat\Enums\ConversationStatus::OPEN)
            <button type="button" class="btn btn-ghost-success btn-sm d-flex align-items-center gap-1" id="close-conversation" data-url="{{ route('fob-live-chat.conversations.close', $conversation->id) }}">
                <x-core::icon name="ti ti-check" />
                {{ trans('plugins/fob-live-chat::live-chat.close_conversation') }}
            </button>
        @endif
    </div>
</div>

{{-- Messages --}}
<div class="fob-chat-messages" id="chat-messages">
    @if($conversation->messages->isEmpty())
        <div class="fob-chat-empty-messages">
            <div class="fob-chat-empty-icon">
                <x-core::icon name="ti ti-message-dots" style="width: 48px; height: 48px;" />
            </div>
            <p class="fob-chat-empty-text">{{ trans('plugins/fob-live-chat::live-chat.no_messages_yet') }}</p>
            <p class="fob-chat-empty-hint">{{ trans('plugins/fob-live-chat::live-chat.start_conversation_hint') }}</p>
        </div>
    @else
        @php
            $lastDate = null;
        @endphp

        @foreach($conversation->messages as $message)
            @php
                $messageDate = $message->created_at->format('d/m/Y');
            @endphp

            {{-- Date Divider --}}
            @if($lastDate !== $messageDate)
                <div class="fob-date-divider">
                    <span>{{ $message->created_at->format('d/m/y') }}</span>
                </div>
                @php $lastDate = $messageDate; @endphp
            @endif

            {{-- Message --}}
            <div class="fob-message-row {{ $message->is_from_admin ? 'fob-message-row-admin' : 'fob-message-row-visitor' }}">
                <div class="fob-message-bubble">
                    <span class="fob-message-text">{{ trim($message->content) }}</span>
                </div>
                <div class="fob-message-time">{{ $message->created_at->format('H:i') }}</div>
            </div>
        @endforeach
    @endif
</div>

{{-- Input --}}
@if($conversation->status->getValue() === \FriendsOfBotble\LiveChat\Enums\ConversationStatus::OPEN)
    <div class="fob-chat-footer">
        <form id="reply-form" action="{{ route('fob-live-chat.conversations.reply', $conversation->id) }}" method="POST">
            @csrf
            <div class="fob-chat-input-group">
                <input type="text" name="message" class="form-control" placeholder="{{ trans('plugins/fob-live-chat::live-chat.type_reply') }}" required maxlength="2000" autocomplete="off">
                <button type="submit" class="btn btn-primary">
                    <x-core::icon name="ti ti-send" />
                </button>
            </div>
        </form>
    </div>
@else
    <div class="fob-closed-alert">
        <x-core::icon name="ti ti-info-circle" class="me-2" />
        {{ trans('plugins/fob-live-chat::live-chat.conversation_is_closed') }}
    </div>
@endif
