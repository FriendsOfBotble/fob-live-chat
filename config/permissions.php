<?php

return [
    [
        'name' => 'Live Chat',
        'flag' => 'fob-live-chat.conversations.index',
    ],
    [
        'name' => 'View',
        'flag' => 'fob-live-chat.conversations.show',
        'parent_flag' => 'fob-live-chat.conversations.index',
    ],
    [
        'name' => 'Reply',
        'flag' => 'fob-live-chat.conversations.reply',
        'parent_flag' => 'fob-live-chat.conversations.index',
    ],
    [
        'name' => 'Delete',
        'flag' => 'fob-live-chat.conversations.destroy',
        'parent_flag' => 'fob-live-chat.conversations.index',
    ],
    [
        'name' => 'Settings',
        'flag' => 'fob-live-chat.settings',
        'parent_flag' => 'fob-live-chat.conversations.index',
    ],
];
