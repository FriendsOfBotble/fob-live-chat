$(() => {
    const $container = $('#fob-live-chat');
    if (!$container.length) return;

    const config = $container.data('config');
    let conversationStarted = false;
    let lastMessageTime = null;
    let pollTimer = null;
    let isWindowOpen = false;

    const $button = $container.find('.fob-chat-button');
    const $window = $container.find('.fob-chat-window');
    const $startForm = $container.find('.fob-chat-start');
    const $body = $container.find('.fob-chat-body');
    const $footer = $container.find('.fob-chat-footer');
    const $messages = $container.find('.fob-chat-messages');
    const $badge = $container.find('.fob-chat-badge');
    const $iconChat = $button.find('.fob-icon-chat');
    const $iconClose = $button.find('.fob-icon-close');

    // CSRF setup for all Ajax requests
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': config.csrfToken }
    });

    // Toggle chat window
    function toggleWindow() {
        isWindowOpen = !isWindowOpen;
        $window.attr('data-status', isWindowOpen ? 'open' : 'closed');
        $button.attr('data-active', isWindowOpen ? 'true' : 'false');

        if (isWindowOpen) {
            $iconChat.hide();
            $iconClose.show();
            if (conversationStarted) {
                startPolling();
                scrollToBottom();
            }
            $container.find('.fob-input:visible:first').focus();
        } else {
            $iconChat.show();
            $iconClose.hide();
            stopPolling();
        }
    }

    $button.on('click', toggleWindow);

    $container.find('.fob-chat-close').on('click', () => {
        if (isWindowOpen) toggleWindow();
    });

    // Start chat form submission
    $container.on('submit', '.fob-chat-start-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $btn = $form.find('button[type="submit"]');

        $btn.prop('disabled', true).addClass('fob-loading');

        $.ajax({
            url: config.startUrl,
            type: 'POST',
            data: $form.serialize() + '&current_url=' + encodeURIComponent(window.location.href),
            dataType: 'json',
            success: (res) => {
                if (!res.error) {
                    conversationStarted = true;
                    $startForm.hide();
                    $body.show();
                    $footer.show();

                    // Add welcome message if set
                    if (config.welcomeMessage) {
                        appendMessage({
                            content: config.welcomeMessage,
                            is_from_admin: true,
                            admin_name: 'Support',
                            created_at: new Date().toISOString()
                        });
                    }

                    startPolling();
                    $footer.find('.fob-input').focus();
                }
            },
            error: (xhr) => {
                const message = xhr.responseJSON?.message || 'Error starting chat';
                showError(message);
            },
            complete: () => {
                $btn.prop('disabled', false).removeClass('fob-loading');
            }
        });
    });

    // Send message form submission
    $container.on('submit', '.fob-chat-send-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $input = $form.find('input[name="message"]');
        const message = $input.val().trim();

        if (!message) return;

        // Optimistic update - show message immediately
        appendMessage({
            content: message,
            is_from_admin: false,
            created_at: new Date().toISOString()
        });
        $input.val('');

        $.ajax({
            url: config.sendUrl,
            type: 'POST',
            data: { message: message },
            dataType: 'json',
            success: (res) => {
                if (res.data?.message?.created_at) {
                    lastMessageTime = res.data.message.created_at;
                }
            },
            error: (xhr) => {
                const errorMsg = xhr.responseJSON?.message || 'Failed to send message';
                showError(errorMsg);
            }
        });
    });

    // Polling functions
    function startPolling() {
        if (pollTimer) return;
        fetchMessages();
        pollTimer = setInterval(fetchMessages, config.pollInterval);
    }

    function stopPolling() {
        if (pollTimer) {
            clearInterval(pollTimer);
            pollTimer = null;
        }
    }

    function fetchMessages() {
        $.ajax({
            url: config.messagesUrl,
            type: 'GET',
            data: lastMessageTime ? { after: lastMessageTime } : {},
            dataType: 'json',
            success: (res) => {
                if (res.data?.messages && res.data.messages.length > 0) {
                    res.data.messages.forEach(msg => {
                        // Only show admin messages from polling (visitor messages are shown optimistically)
                        if (msg.is_from_admin) {
                            appendMessage(msg);
                        }
                        if (msg.created_at) {
                            lastMessageTime = msg.created_at;
                        }
                    });
                }

                // Check if conversation exists but we haven't set started flag
                if (res.data?.has_conversation && !conversationStarted) {
                    conversationStarted = true;
                    $startForm.hide();
                    $body.show();
                    $footer.show();
                }
            }
        });
    }

    function appendMessage(msg) {
        const isAdmin = msg.is_from_admin;
        const cls = isAdmin ? 'fob-msg-admin' : 'fob-msg-visitor';

        // Escape HTML to prevent XSS
        const safeContent = $('<div>').text(msg.content).html();

        let html = `<div class="fob-msg ${cls}">`;
        if (isAdmin && msg.admin_name) {
            html += `<div class="fob-msg-sender">${$('<div>').text(msg.admin_name).html()}</div>`;
        }
        html += `<div class="fob-msg-content">${safeContent}</div>`;
        html += `</div>`;

        $messages.append(html);
        scrollToBottom();
    }

    function scrollToBottom() {
        $messages.scrollTop($messages[0].scrollHeight);
    }

    function showError(message) {
        // Simple error notification
        const $error = $('<div class="fob-toast fob-toast-error">' + $('<div>').text(message).html() + '</div>');
        $container.append($error);
        setTimeout(() => $error.fadeOut(300, function() { $(this).remove(); }), 3000);
    }

    // Check for existing conversation on page load
    $.ajax({
        url: config.messagesUrl,
        type: 'GET',
        dataType: 'json',
        success: (res) => {
            if (res.data?.has_conversation && res.data?.messages?.length > 0) {
                conversationStarted = true;
                $startForm.hide();
                $body.show();
                $footer.show();

                res.data.messages.forEach(msg => {
                    appendMessage(msg);
                    if (msg.created_at) {
                        lastMessageTime = msg.created_at;
                    }
                });

                // Show unread count badge
                const unreadCount = res.data.messages.filter(m => m.is_from_admin && !m.is_read).length;
                if (unreadCount > 0) {
                    $badge.text(unreadCount).show();
                }
            }
        }
    });

    // Handle Enter key in message input
    $container.on('keydown', '.fob-chat-send-form input', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            $(this).closest('form').submit();
        }
    });

    // Close on Escape key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && isWindowOpen) {
            toggleWindow();
        }
    });
});
