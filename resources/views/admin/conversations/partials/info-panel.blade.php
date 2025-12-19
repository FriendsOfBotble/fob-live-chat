{{-- Info Header --}}
<div class="fob-info-header">
    <div class="fob-conversation-avatar {{ $conversation->status->getValue() === 'open' ? 'is-online' : '' }}">
        <span class="fob-avatar-text">{{ strtoupper(substr($conversation->visitor_name, 0, 2)) }}</span>
    </div>
    <div class="fob-info-header-name">{{ $conversation->visitor_name }}</div>
    @if($conversation->visitor_email)
        <div class="fob-info-header-email">
            <a href="mailto:{{ $conversation->visitor_email }}" class="text-secondary">{{ $conversation->visitor_email }}</a>
        </div>
    @endif
    <div class="fob-info-header-status mt-2">
        {!! $conversation->status->toHtml() !!}
    </div>
</div>

{{-- Visitor Info Section --}}
<div class="fob-info-section">
    <div class="fob-info-section-title">{{ trans('plugins/fob-live-chat::live-chat.visitor_info') }}</div>

    <div class="fob-info-item">
        <div class="fob-info-icon">
            <x-core::icon name="ti ti-world" />
        </div>
        <div class="fob-info-content">
            <div class="fob-info-label">{{ trans('plugins/fob-live-chat::live-chat.ip_address') }}</div>
            <div class="fob-info-value">
                @if($conversation->visitor_ip)
                    <code class="text-muted">{{ $conversation->visitor_ip }}</code>
                @else
                    <span class="text-muted">-</span>
                @endif
            </div>
        </div>
    </div>

    <div class="fob-info-item">
        <div class="fob-info-icon">
            <x-core::icon name="ti ti-phone" />
        </div>
        <div class="fob-info-content">
            <div class="fob-info-label">{{ trans('plugins/fob-live-chat::live-chat.visitor_phone') }}</div>
            <div class="fob-info-value">
                @if($conversation->visitor_phone)
                    <a href="tel:{{ $conversation->visitor_phone }}" class="text-muted">{{ $conversation->visitor_phone }}</a>
                @else
                    <span class="text-muted">-</span>
                @endif
            </div>
        </div>
    </div>

    @if($conversation->current_url)
        @php
            $parsedUrl = parse_url($conversation->current_url);
            $displayPath = ($parsedUrl['path'] ?? '/') . (isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '');
        @endphp
        <div class="fob-info-item">
            <div class="fob-info-icon">
                <x-core::icon name="ti ti-link" />
            </div>
            <div class="fob-info-content">
                <div class="fob-info-label">{{ trans('plugins/fob-live-chat::live-chat.current_page') }}</div>
                <div class="fob-info-value">
                    <a href="{{ $conversation->current_url }}" target="_blank" rel="noopener" class="fob-url-link" title="{{ $conversation->current_url }}">
                        <span class="fob-url-path">{{ Str::limit($displayPath, 30) }}</span>
                        <x-core::icon name="ti ti-external-link" class="fob-url-icon" />
                    </a>
                </div>
            </div>
        </div>
    @endif

    @if($conversation->visitor_user_agent)
        @php
            $ua = $conversation->visitor_user_agent;
            $browser = 'Unknown';
            $device = 'Desktop';

            if (str_contains($ua, 'Chrome')) $browser = 'Chrome';
            elseif (str_contains($ua, 'Firefox')) $browser = 'Firefox';
            elseif (str_contains($ua, 'Safari')) $browser = 'Safari';
            elseif (str_contains($ua, 'Edge')) $browser = 'Edge';

            if (str_contains($ua, 'Mobile')) $device = 'Mobile';
            elseif (str_contains($ua, 'Tablet')) $device = 'Tablet';
        @endphp
        <div class="fob-info-item">
            <div class="fob-info-icon">
                <x-core::icon name="ti ti-device-desktop" />
            </div>
            <div class="fob-info-content">
                <div class="fob-info-label">{{ trans('plugins/fob-live-chat::live-chat.browser') }}</div>
                <div class="fob-info-value">{{ $browser }} ({{ $device }})</div>
            </div>
        </div>
    @endif
</div>

{{-- Timeline Section --}}
<div class="fob-info-section">
    <div class="fob-info-section-title">{{ trans('plugins/fob-live-chat::live-chat.timeline') }}</div>

    <div class="fob-info-item">
        <div class="fob-info-icon">
            <x-core::icon name="ti ti-clock" />
        </div>
        <div class="fob-info-content">
            <div class="fob-info-label">{{ trans('core/base::tables.created_at') }}</div>
            <div class="fob-info-value">{{ $conversation->created_at->diffForHumans() }}</div>
        </div>
    </div>

    <div class="fob-info-item">
        <div class="fob-info-icon">
            <x-core::icon name="ti ti-message" />
        </div>
        <div class="fob-info-content">
            <div class="fob-info-label">{{ trans('plugins/fob-live-chat::live-chat.last_message') }}</div>
            <div class="fob-info-value">{{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '-' }}</div>
        </div>
    </div>

    <div class="fob-info-item">
        <div class="fob-info-icon">
            <x-core::icon name="ti ti-messages" />
        </div>
        <div class="fob-info-content">
            <div class="fob-info-label">{{ trans('plugins/fob-live-chat::live-chat.total_messages') }}</div>
            <div class="fob-info-value">
                <span class="badge badge-pill bg-azure-lt">{{ $conversation->messages->count() }}</span>
            </div>
        </div>
    </div>
</div>

{{-- Actions Section --}}
<div class="fob-info-actions">
    <button type="button" class="btn btn-outline-danger btn-sm d-flex align-items-center justify-content-center gap-2" id="delete-conversation" data-url="{{ route('fob-live-chat.conversations.destroy', $conversation->id) }}">
        <x-core::icon name="ti ti-trash" />
        {{ trans('plugins/fob-live-chat::live-chat.delete_conversation') }}
    </button>
</div>
