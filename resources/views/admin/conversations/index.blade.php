@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="fob-messenger">
        {{-- Conversations List --}}
        <div class="fob-messenger-sidebar">
            <div class="fob-messenger-sidebar-header">
                <div class="fob-sidebar-title">
                    <x-core::icon name="ti ti-messages" class="text-primary" style="width: 22px; height: 22px;" />
                    <h4>{{ trans('plugins/fob-live-chat::live-chat.title') }}</h4>
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
                                <span class="fob-conversation-preview">{{ Str::limit($conv->last_message, 50) }}</span>
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
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/fob-live-chat/css/admin-messenger.css') }}">
@endpush

@push('footer')
    <script>
        $(function() {
            const $conversationsList = $('#conversations-list');
            const $chatPanel = $('#chat-panel');
            const $infoPanel = $('#info-panel');
            let currentConversationId = @json($selectedId);
            let lastMessageId = @json($selected?->messages->last()?->id);
            let pollInterval = null;

            // Conversation click
            $conversationsList.on('click', '.fob-conversation-item', function() {
                const $item = $(this);
                const id = $item.data('id');

                if (id === currentConversationId) return;

                $conversationsList.find('.fob-conversation-item').removeClass('active');
                $item.addClass('active');
                $item.find('.fob-unread-badge').remove();

                loadConversation(id);
            });

            // Filter tabs
            $('.fob-filter-tab').on('click', function() {
                const $tab = $(this);
                const filter = $tab.data('filter');

                $('.fob-filter-tab').removeClass('active');
                $tab.addClass('active');

                $conversationsList.find('.fob-conversation-item').each(function() {
                    const $item = $(this);
                    const status = $item.data('status');

                    if (filter === 'all' || status === filter) {
                        $item.show();
                    } else {
                        $item.hide();
                    }
                });
            });

            // Load conversation
            function loadConversation(id) {
                stopPolling();
                currentConversationId = id;

                // Update URL without reload
                const url = new URL(window.location);
                url.searchParams.set('id', id);
                window.history.pushState({}, '', url);

                $.ajax({
                    url: '{{ route('fob-live-chat.conversations.show', ':id') }}'.replace(':id', id),
                    type: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    success: function(res) {
                        if (res.error) {
                            Botble.showError(res.message);
                            return;
                        }

                        const data = res.data;
                        $chatPanel.html(data.chat_panel);
                        $infoPanel.html(data.info_panel).removeClass('d-none');
                        lastMessageId = data.last_message_id || 0;

                        if (data.is_open) {
                            startPolling();
                        }

                        bindChatEvents();
                        scrollToBottom();
                    },
                    error: function(xhr) {
                        Botble.handleError(xhr);
                    }
                });
            }

            // Bind chat form events
            function bindChatEvents() {
                const $replyForm = $('#reply-form');
                const $closeBtn = $('#close-conversation');
                const $deleteBtn = $('#delete-conversation');

                $replyForm.off('submit').on('submit', function(e) {
                    e.preventDefault();
                    const $input = $replyForm.find('input[name="message"]');
                    const message = $input.val().trim();

                    if (!message) return;

                    const $btn = $replyForm.find('button[type="submit"]');
                    $btn.prop('disabled', true);

                    $.ajax({
                        url: $replyForm.attr('action'),
                        type: 'POST',
                        data: $replyForm.serialize(),
                        dataType: 'json',
                        success: function(res) {
                            if (!res.error) {
                                $input.val('');
                                fetchNewMessages();
                                updateConversationPreview(message);
                            }
                        },
                        error: function(xhr) {
                            Botble.handleError(xhr);
                        },
                        complete: function() {
                            $btn.prop('disabled', false);
                            $input.focus();
                        }
                    });
                });

                $closeBtn.off('click').on('click', function() {
                    if (!confirm('{{ trans('plugins/fob-live-chat::live-chat.confirm_close') }}')) return;

                    $.ajax({
                        url: $(this).data('url'),
                        type: 'POST',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json',
                        success: function(res) {
                            if (!res.error) {
                                Botble.showSuccess(res.message);
                                loadConversation(currentConversationId);
                                updateConversationStatus('closed');
                            }
                        },
                        error: function(xhr) {
                            Botble.handleError(xhr);
                        }
                    });
                });

                $deleteBtn.off('click').on('click', function() {
                    if (!confirm('{{ trans('plugins/fob-live-chat::live-chat.confirm_delete') }}')) return;

                    $.ajax({
                        url: $(this).data('url'),
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        dataType: 'json',
                        success: function(res) {
                            if (!res.error) {
                                Botble.showSuccess(res.message);
                                removeConversationFromList(currentConversationId);
                                showEmptyState();
                            }
                        },
                        error: function(xhr) {
                            Botble.handleError(xhr);
                        }
                    });
                });
            }

            // Polling for new messages
            function startPolling() {
                if (pollInterval) return;
                pollInterval = setInterval(fetchNewMessages, 5000);
            }

            function stopPolling() {
                if (pollInterval) {
                    clearInterval(pollInterval);
                    pollInterval = null;
                }
            }

            function fetchNewMessages() {
                if (!currentConversationId) return;

                $.ajax({
                    url: '{{ route('fob-live-chat.conversations.messages', ':id') }}'.replace(':id', currentConversationId),
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
                const $messages = $('#chat-messages');
                const isAdmin = msg.is_from_admin;

                const html = `
                    <div class="fob-message-row ${isAdmin ? 'fob-message-row-admin' : 'fob-message-row-visitor'}">
                        <div class="fob-message-bubble">
                            <div class="fob-message-text">${escapeHtml(msg.content)}</div>
                        </div>
                        <div class="fob-message-time">${msg.created_at}</div>
                    </div>
                `;
                $messages.append(html);
                scrollToBottom();
            }

            function scrollToBottom() {
                const $messages = $('#chat-messages');
                if ($messages.length) {
                    $messages.scrollTop($messages[0].scrollHeight);
                }
            }

            function escapeHtml(text) {
                return $('<div>').text(text).html();
            }

            function updateConversationPreview(message) {
                const $item = $(`.fob-conversation-item[data-id="${currentConversationId}"]`);
                $item.find('.fob-conversation-preview').text(message.substring(0, 50));
                $item.find('.fob-conversation-time').text('{{ trans('plugins/fob-live-chat::live-chat.just_now') }}');

                // Remove unread badge if exists (we just sent a message)
                $item.find('.fob-unread-badge').remove();
                $item.removeClass('has-unread');

                // Move to top with animation
                $item.slideUp(150, function() {
                    $item.prependTo($conversationsList).slideDown(150);
                });
            }

            function updateConversationStatus(status) {
                const $item = $(`.fob-conversation-item[data-id="${currentConversationId}"]`);
                $item.data('status', status);

                // Update avatar online status
                const $avatar = $item.find('.fob-conversation-avatar');
                if (status === 'open') {
                    $avatar.addClass('is-online');
                } else {
                    $avatar.removeClass('is-online');
                }

                // Update status dot
                const $statusDot = $item.find('.fob-status-dot');
                $statusDot.removeClass('is-open is-closed').addClass(status === 'open' ? 'is-open' : 'is-closed');
            }

            function removeConversationFromList(id) {
                $(`.fob-conversation-item[data-id="${id}"]`).fadeOut(300, function() {
                    $(this).remove();
                });
                currentConversationId = null;
            }

            function showEmptyState() {
                $chatPanel.html(`
                    <div class="fob-empty-chat">
                        <div class="fob-empty-chat-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler">
                                <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                <path d="M3 20l1.3 -3.9c-2.324 -3.437 -1.426 -7.872 2.1 -10.374c3.526 -2.501 8.59 -2.296 11.845 .48c3.255 2.777 3.695 7.266 1.029 10.501c-2.666 3.235 -7.615 4.215 -11.574 2.293l-4.7 1" />
                            </svg>
                        </div>
                        <h3 class="fob-empty-chat-title">{{ trans('plugins/fob-live-chat::live-chat.select_conversation') }}</h3>
                        <p class="fob-empty-chat-desc">{{ trans('plugins/fob-live-chat::live-chat.select_conversation_hint') }}</p>
                    </div>
                `);
                $infoPanel.addClass('d-none').html('');
            }

            // Initial setup
            if (currentConversationId) {
                bindChatEvents();
                scrollToBottom();
                const $item = $(`.fob-conversation-item[data-id="${currentConversationId}"]`);
                if ($item.data('status') === 'open') {
                    startPolling();
                }
            }

            // Poll for new conversations
            setInterval(function() {
                $.ajax({
                    url: '{{ route('fob-live-chat.conversations.index') }}',
                    type: 'GET',
                    data: { check_updates: 1 },
                    dataType: 'json',
                    success: function(res) {
                        if (res.unread_count !== undefined) {
                            // Update badge counts
                        }
                    }
                });
            }, 30000);
        });
    </script>
@endpush
