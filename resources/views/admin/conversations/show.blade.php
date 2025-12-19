@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{ trans('plugins/fob-live-chat::live-chat.messages') }}</h4>
                    <div>
                        @if($conversation->status->getValue() === \FriendsOfBotble\LiveChat\Enums\ConversationStatus::OPEN)
                            <button type="button" class="btn btn-secondary btn-sm" id="close-conversation">
                                {{ trans('plugins/fob-live-chat::live-chat.close_conversation') }}
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="chat-messages-container" id="chat-messages" style="height: 400px; overflow-y: auto; padding: 20px;">
                        @foreach($conversation->messages as $message)
                            <div class="chat-message mb-3 d-flex {{ $message->is_from_admin ? 'justify-content-end' : 'justify-content-start' }}">
                                <div class="message-bubble p-3 rounded-3 {{ $message->is_from_admin ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 75%;">
                                    @if($message->is_from_admin && $message->admin_name)
                                        <div class="message-sender small mb-1 {{ $message->is_from_admin ? 'text-white-50' : 'text-muted' }}">
                                            {{ $message->admin_name }}
                                        </div>
                                    @endif
                                    <div class="message-content">{{ $message->content }}</div>
                                    <div class="message-time small {{ $message->is_from_admin ? 'text-white-50' : 'text-muted' }} mt-1">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if($conversation->status->getValue() === \FriendsOfBotble\LiveChat\Enums\ConversationStatus::OPEN)
                        <div class="chat-input-container border-top p-3">
                            <form id="reply-form" action="{{ route('fob-live-chat.conversations.reply', $conversation->id) }}" method="POST">
                                @csrf
                                <div class="input-group">
                                    <input type="text" name="message" class="form-control" placeholder="{{ trans('plugins/fob-live-chat::live-chat.type_reply') }}" required maxlength="2000" autocomplete="off">
                                    <button type="submit" class="btn btn-primary">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="22" y1="2" x2="11" y2="13"></line>
                                            <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                        </svg>
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-secondary m-3">
                            {{ trans('plugins/fob-live-chat::live-chat.conversation_is_closed') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ trans('plugins/fob-live-chat::live-chat.visitor_info') }}</h4>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">{{ trans('plugins/fob-live-chat::live-chat.visitor_name') }}</dt>
                        <dd class="col-sm-8">{{ $conversation->visitor_name }}</dd>

                        <dt class="col-sm-4">{{ trans('plugins/fob-live-chat::live-chat.visitor_email') }}</dt>
                        <dd class="col-sm-8">{{ $conversation->visitor_email ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ trans('plugins/fob-live-chat::live-chat.visitor_phone') }}</dt>
                        <dd class="col-sm-8">{{ $conversation->visitor_phone ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ trans('core/base::tables.status') }}</dt>
                        <dd class="col-sm-8">{!! $conversation->status->toHtml() !!}</dd>

                        <dt class="col-sm-4">{{ trans('plugins/fob-live-chat::live-chat.ip_address') }}</dt>
                        <dd class="col-sm-8">{{ $conversation->visitor_ip ?: '-' }}</dd>

                        <dt class="col-sm-4">{{ trans('core/base::tables.created_at') }}</dt>
                        <dd class="col-sm-8">{{ $conversation->created_at->format('d/m/Y H:i') }}</dd>

                        <dt class="col-sm-4">{{ trans('plugins/fob-live-chat::live-chat.last_message') }}</dt>
                        <dd class="col-sm-8">{{ $conversation->last_message_at ? $conversation->last_message_at->format('d/m/Y H:i') : '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('footer')
    <script>
        $(function() {
            const $messages = $('#chat-messages');
            const conversationId = {{ $conversation->id }};
            let lastMessageId = {{ $conversation->messages->last()?->id ?? 0 }};
            let pollInterval;

            // Scroll to bottom on load
            $messages.scrollTop($messages[0].scrollHeight);

            // Reply form submission
            $('#reply-form').on('submit', function(e) {
                e.preventDefault();
                const $form = $(this);
                const $input = $form.find('input[name="message"]');
                const message = $input.val().trim();

                if (!message) return;

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: $form.serialize(),
                    dataType: 'json',
                    success: function(res) {
                        if (!res.error) {
                            $input.val('');
                            fetchNewMessages();
                            Botble.showSuccess(res.message);
                        }
                    },
                    error: function(xhr) {
                        Botble.handleError(xhr);
                    }
                });
            });

            // Close conversation
            $('#close-conversation').on('click', function() {
                if (!confirm('{{ trans('plugins/fob-live-chat::live-chat.confirm_close') }}')) return;

                $.ajax({
                    url: '{{ route('fob-live-chat.conversations.close', $conversation->id) }}',
                    type: 'POST',
                    data: { _token: '{{ csrf_token() }}' },
                    dataType: 'json',
                    success: function(res) {
                        if (!res.error) {
                            Botble.showSuccess(res.message);
                            setTimeout(() => location.reload(), 1000);
                        }
                    },
                    error: function(xhr) {
                        Botble.handleError(xhr);
                    }
                });
            });

            // Poll for new messages
            function fetchNewMessages() {
                $.ajax({
                    url: '{{ route('fob-live-chat.conversations.messages', $conversation->id) }}',
                    type: 'GET',
                    data: { after_id: lastMessageId },
                    dataType: 'json',
                    success: function(res) {
                        if (res.data?.messages?.length > 0) {
                            res.data.messages.forEach(function(msg) {
                                if (msg.id > lastMessageId) {
                                    appendMessage(msg);
                                    lastMessageId = msg.id;
                                }
                            });
                        }
                    }
                });
            }

            function appendMessage(msg) {
                const isAdmin = msg.is_from_admin;
                const senderHtml = isAdmin && msg.admin_name ?
                    `<div class="message-sender small mb-1 ${isAdmin ? 'text-white-50' : 'text-muted'}">${escapeHtml(msg.admin_name)}</div>` : '';

                const html = `
                    <div class="chat-message mb-3 d-flex ${isAdmin ? 'justify-content-end' : 'justify-content-start'}">
                        <div class="message-bubble p-3 rounded-3 ${isAdmin ? 'bg-primary text-white' : 'bg-light'}" style="max-width: 75%;">
                            ${senderHtml}
                            <div class="message-content">${escapeHtml(msg.content)}</div>
                            <div class="message-time small ${isAdmin ? 'text-white-50' : 'text-muted'} mt-1">${msg.created_at}</div>
                        </div>
                    </div>
                `;
                $messages.append(html);
                $messages.scrollTop($messages[0].scrollHeight);
            }

            function escapeHtml(text) {
                return $('<div>').text(text).html();
            }

            @if($conversation->status->getValue() === \FriendsOfBotble\LiveChat\Enums\ConversationStatus::OPEN)
                pollInterval = setInterval(fetchNewMessages, 5000);
            @endif
        });
    </script>
@endpush
