@extends(BaseHelper::getAdminMasterLayoutTemplate())

@section('content')
    <div class="fob-messenger">
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
    <link rel="stylesheet" href="{{ asset('vendor/core/plugins/fob-live-chat/css/admin-messenger.css') }}">
@endpush

@push('footer')
    <script>
        $(function() {
            const $conversationsList = $('#conversations-list');
            const $chatPanel = $('#chat-panel');
            const $infoPanel = $('#info-panel');
            let currentConversationId = @json($selectedId);
            let lastMessageId = @json($selected?->messages->last()?->id ?? 0);
            let pollInterval = null;
            let updatePollInterval = null;
            let notificationPermission = Notification.permission;
            let originalTitle = document.title;
            let titleBlinkInterval = null;
            let totalUnreadCount = 0;
            let lastKnownUnreads = {};
            const CHECK_UPDATES_INTERVAL = 5000;
            const FETCH_MESSAGES_INTERVAL = 3000;

            // Initialize lastKnownUnreads from existing items
            $conversationsList.find('.fob-conversation-item').each(function() {
                const id = $(this).data('id');
                const $badge = $(this).find('.fob-unread-badge');
                lastKnownUnreads[id] = $badge.length ? parseInt($badge.text()) || 0 : 0;
            });

            function getKnownConversationIds() {
                const ids = [];
                $conversationsList.find('.fob-conversation-item').each(function() {
                    ids.push($(this).data('id'));
                });
                return ids;
            }

            function updateNotificationUI() {
                const $enableBtn = $('#enable-notifications-btn');
                const $enabledBadge = $('#notifications-enabled-badge');

                if (!('Notification' in window)) {
                    $enableBtn.hide();
                    $enabledBadge.hide();
                    return;
                }

                if (notificationPermission === 'granted') {
                    $enableBtn.hide();
                    $enabledBadge.show();
                } else if (notificationPermission === 'default') {
                    $enableBtn.show();
                    $enabledBadge.hide();
                } else {
                    $enableBtn.hide();
                    $enabledBadge.hide();
                }
            }

            function requestNotificationPermission() {
                if ('Notification' in window && notificationPermission === 'default') {
                    Notification.requestPermission().then(function(permission) {
                        notificationPermission = permission;
                        updateNotificationUI();
                        if (permission === 'granted') {
                            Botble.showSuccess('{{ trans('plugins/fob-live-chat::live-chat.notifications_enabled') }}');
                        }
                    });
                }
            }

            $('#enable-notifications-btn').on('click', function() {
                requestNotificationPermission();
                playNotificationSound();
            });

            $('#notifications-enabled-badge').on('click', function() {
                playNotificationSound();
            }).css('cursor', 'pointer').attr('title', '{{ trans('plugins/fob-live-chat::live-chat.test_sound') }}');

            function showBrowserNotification(title, body, conversationId) {
                if (notificationPermission !== 'granted') return;
                if (document.hasFocus()) return;

                const notification = new Notification(title, {
                    body: body.substring(0, 100),
                    icon: '{{ asset('vendor/core/core/base/images/favicon.png') }}',
                    tag: 'live-chat-' + conversationId,
                    requireInteraction: false
                });

                notification.onclick = function() {
                    window.focus();
                    if (conversationId) {
                        loadConversation(conversationId);
                    }
                    notification.close();
                };

                setTimeout(function() {
                    notification.close();
                }, 8000);
            }

            function playNotificationSound() {
                try {
                    const audio = new Audio('{{ asset('vendor/core/plugins/fob-live-chat/sounds/notification.mp3') }}');
                    audio.volume = 0.5;
                    audio.play().catch(() => {});
                } catch (e) {}
            }

            function startTitleBlink(count) {
                stopTitleBlink();
                if (count <= 0) return;

                let isOriginal = true;
                titleBlinkInterval = setInterval(function() {
                    if (document.hasFocus()) {
                        stopTitleBlink();
                        return;
                    }
                    document.title = isOriginal ? `(${count}) {{ trans('plugins/fob-live-chat::live-chat.new_message') }}` : originalTitle;
                    isOriginal = !isOriginal;
                }, 1000);
            }

            function stopTitleBlink() {
                if (titleBlinkInterval) {
                    clearInterval(titleBlinkInterval);
                    titleBlinkInterval = null;
                }
                document.title = originalTitle;
            }

            function updateUnreadBadge($item, count) {
                $item.find('.fob-unread-badge').remove();
                if (count > 0) {
                    const badge = count > 9 ? '9+' : count;
                    $item.find('.fob-conversation-body').append(`<span class="fob-unread-badge">${badge}</span>`);
                    $item.addClass('has-unread');
                    $item.find('.fob-status-dot').hide();
                } else {
                    $item.removeClass('has-unread');
                    $item.find('.fob-status-dot').show();
                }
            }

            function addNewConversation(conv) {
                const noMessagesText = '{{ trans('plugins/fob-live-chat::live-chat.no_messages_yet') }}';
                const previewText = conv.last_message ? escapeHtml(conv.last_message.substring(0, 50)) : noMessagesText;
                const previewClass = conv.last_message ? '' : 'text-muted fst-italic';

                const html = `
                    <div class="fob-conversation-item has-unread" data-id="${conv.id}" data-status="${conv.status}">
                        <div class="fob-conversation-avatar ${conv.status === 'open' ? 'is-online' : ''}">
                            <span class="fob-avatar-text">${escapeHtml(conv.initials)}</span>
                        </div>
                        <div class="fob-conversation-content">
                            <div class="fob-conversation-header">
                                <span class="fob-conversation-name">${escapeHtml(conv.visitor_name)}</span>
                                <span class="fob-conversation-time">${conv.last_message_at || ''}</span>
                            </div>
                            <div class="fob-conversation-body">
                                <span class="fob-conversation-preview ${previewClass}">${previewText}</span>
                                <span class="fob-unread-badge">${conv.unread_count > 9 ? '9+' : conv.unread_count}</span>
                            </div>
                        </div>
                    </div>
                `;

                const $emptyState = $conversationsList.find('.fob-empty-state');
                if ($emptyState.length) {
                    $emptyState.remove();
                }

                const $newItem = $(html).hide().prependTo($conversationsList).slideDown(200);

                updateFilterCounts();
            }

            function updateFilterCounts() {
                const allCount = $conversationsList.find('.fob-conversation-item').length;
                const openCount = $conversationsList.find('.fob-conversation-item[data-status="open"]').length;
                const closedCount = $conversationsList.find('.fob-conversation-item[data-status="closed"]').length;

                $('.fob-filter-tab[data-filter="all"] .fob-filter-count').text(allCount);
                $('.fob-filter-tab[data-filter="open"] .fob-filter-count').text(openCount);
                $('.fob-filter-tab[data-filter="closed"] .fob-filter-count').text(closedCount);
            }

            function checkForUpdates() {
                const knownIds = getKnownConversationIds();

                $.ajax({
                    url: '{{ route('fob-live-chat.conversations.check-updates') }}',
                    type: 'GET',
                    data: {
                        known_ids: JSON.stringify(knownIds),
                        current_id: currentConversationId,
                        after_message_id: lastMessageId
                    },
                    dataType: 'json',
                    success: function(res) {
                        if (res.error) return;

                        const data = res.data;

                        let hasNewNotification = false;

                        if (data.new_conversations && data.new_conversations.length > 0) {
                            data.new_conversations.forEach(function(conv) {
                                addNewConversation(conv);
                                lastKnownUnreads[conv.id] = conv.unread_count;
                                showBrowserNotification(
                                    '{{ trans('plugins/fob-live-chat::live-chat.new_conversation') }}',
                                    conv.visitor_name + ': ' + (conv.last_message || ''),
                                    conv.id
                                );
                                hasNewNotification = true;
                            });
                        }

                        if (data.updates && data.updates.length > 0) {
                            data.updates.forEach(function(update) {
                                const $item = $(`.fob-conversation-item[data-id="${update.id}"]`);
                                if ($item.length) {
                                    const prevUnread = lastKnownUnreads[update.id] || 0;
                                    const newUnread = update.unread_count;

                                    updateUnreadBadge($item, newUnread);
                                    $item.find('.fob-conversation-preview').text((update.last_message || '').substring(0, 50));
                                    $item.find('.fob-conversation-time').text(update.last_message_at || '');

                                    if (newUnread > prevUnread) {
                                        hasNewNotification = true;
                                        if (update.id !== currentConversationId) {
                                            showBrowserNotification(
                                                '{{ trans('plugins/fob-live-chat::live-chat.new_message') }}',
                                                update.last_message || '',
                                                update.id
                                            );
                                        }
                                    }

                                    lastKnownUnreads[update.id] = newUnread;
                                }
                            });
                        }

                        if (hasNewNotification) {
                            playNotificationSound();
                        }

                        if (data.new_messages && data.new_messages.length > 0) {
                            data.new_messages.forEach(function(msg) {
                                if (msg.id > lastMessageId) {
                                    appendMessage(msg);
                                    lastMessageId = msg.id;
                                }
                            });
                        }

                        if (data.total_unread !== totalUnreadCount) {
                            totalUnreadCount = data.total_unread;
                            if (totalUnreadCount > 0 && !document.hasFocus()) {
                                startTitleBlink(totalUnreadCount);
                            }
                        }
                    }
                });
            }

            function startUpdatePolling() {
                if (updatePollInterval) return;
                checkForUpdates();
                updatePollInterval = setInterval(checkForUpdates, CHECK_UPDATES_INTERVAL);
            }

            function stopUpdatePolling() {
                if (updatePollInterval) {
                    clearInterval(updatePollInterval);
                    updatePollInterval = null;
                }
            }

            $(window).on('focus', function() {
                stopTitleBlink();
            });

            updateNotificationUI();

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

            startUpdatePolling();
        });
    </script>
@endpush
