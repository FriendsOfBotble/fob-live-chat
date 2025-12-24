<?php

return [
    'name' => 'plugins/fob-live-chat::live-chat.email.title',
    'description' => 'plugins/fob-live-chat::live-chat.email.description',
    'templates' => [
        'new-conversation' => [
            'title' => 'plugins/fob-live-chat::live-chat.email.templates.new_conversation_title',
            'description' => 'plugins/fob-live-chat::live-chat.email.templates.new_conversation_description',
            'subject' => 'plugins/fob-live-chat::live-chat.email.templates.new_conversation_subject',
            'can_off' => true,
            'enabled' => true,
            'variables' => [
                'visitor_name' => 'plugins/fob-live-chat::live-chat.email.templates.visitor_name',
                'visitor_email' => 'plugins/fob-live-chat::live-chat.email.templates.visitor_email',
                'visitor_phone' => 'plugins/fob-live-chat::live-chat.email.templates.visitor_phone',
                'visitor_ip' => 'plugins/fob-live-chat::live-chat.email.templates.visitor_ip',
                'current_page' => 'plugins/fob-live-chat::live-chat.email.templates.current_page',
                'conversation_url' => 'plugins/fob-live-chat::live-chat.email.templates.conversation_url',
            ],
        ],
    ],
];
