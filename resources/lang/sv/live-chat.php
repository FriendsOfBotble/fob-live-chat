<?php

return [
    'title' => 'Direktchatt',
    'messages' => 'Meddelanden',
    'visitor_name' => 'Besökarens namn',
    'visitor_email' => 'Besökarens e-post',
    'visitor_phone' => 'Telefon',
    'visitor_info' => 'Besökarinformation',
    'ip_address' => 'IP-adress',
    'current_page' => 'Aktuell sida',
    'browser' => 'Webbläsare',
    'last_message' => 'Senaste meddelande',
    'close_conversation' => 'Stäng',
    'delete_conversation' => 'Ta bort konversation',
    'conversation_closed' => 'Konversationen har stängts.',
    'conversation_deleted' => 'Konversationen har tagits bort.',
    'conversation_is_closed' => 'Den här konversationen är stängd.',
    'conversation_with' => 'Konversation med :name',
    'confirm_close' => 'Är du säker på att du vill stänga den här konversationen?',
    'confirm_delete' => 'Är du säker på att du vill ta bort den här konversationen? Den här åtgärden kan inte ångras.',
    'type_reply' => 'Skriv ditt svar...',
    'reply_sent' => 'Svaret skickades.',
    'no_conversation' => 'Ingen aktiv konversation hittades. Starta en ny chatt.',
    'no_conversations' => 'Inga konversationer ännu',
    'chat_started' => 'Chatten startades.',
    'message_sent' => 'Meddelande skickat.',
    'select_conversation' => 'Välj en konversation',
    'select_conversation_hint' => 'Välj en konversation från listan för att börja chatta.',
    'all' => 'Alla',
    'timeline' => 'Tidslinje',
    'total_messages' => 'Totalt antal meddelanden',
    'just_now' => 'Precis nu',
    'test_sound' => 'Klicka för att testa ljud',
    'no_messages_yet' => 'Inga meddelanden ännu',
    'start_conversation_hint' => 'Skicka ett meddelande för att starta konversationen',
    'notifications_enabled' => 'Webbläsaraviseringar aktiverade!',
    'enable_notifications' => 'Aktivera webbläsaraviseringar',
    'new_conversation' => 'Ny konversation',
    'new_message' => 'Nytt meddelande',
    'no_conversations_hint' => 'Konversationer visas här när besökare börjar chatta.',
    'open_chat' => 'Öppna chatt',
    'close' => 'Stäng',
    'chat_title' => 'Direktchatt',
    'online' => 'Ansluten',
    'start_message' => 'Hej! Hur kan vi hjälpa dig idag?',
    'your_name' => 'Ditt namn',
    'your_email' => 'Din e-post',
    'your_email_optional' => 'Din e-post (valfritt)',
    'your_phone' => 'Din telefon',
    'start_chat' => 'Starta chatt',
    'type_message' => 'Skriv ett meddelande...',
    'send' => 'Skicka',
    'default_admin_name' => 'Support',
    'default_welcome_message' => 'Hej! Tack för att du hör av dig. Hur kan jag hjälpa dig idag?',
    'conversation_statuses' => [
        'open' => 'Öppen',
        'closed' => 'Stängd',
    ],
    'settings' => [
        'title' => 'Direktchatt',
        'description' => 'Konfigurera den flytande direktchatt-widgeten som visas på din webbplats för kundsupport i realtid.',
        'form' => [
            'enabled' => 'Aktivera direktchatt',
            'enabled_help' => 'Slå på för att visa chatt-widgeten på din webbplats. Besökare kan starta konversationer med ditt supportteam.',
            'widget_title' => 'Widgettitel',
            'widget_title_help' => 'Titeln som visas högst upp i chatt-widgetens header.',
            'widget_title_placeholder' => 'Direktchatt',
            'welcome_message' => 'Välkomstmeddelande',
            'welcome_message_help' => 'Det första meddelandet som besökare ser när de öppnar chatt-widgeten. Lämna tomt för att använda standardmeddelandet.',
            'welcome_message_placeholder' => 'Hej! Tack för att du hör av dig. Hur kan jag hjälpa dig idag?',
            'admin_name' => 'Adminnamn',
            'admin_name_help' => 'Namnet som visas för adminsvar i chattwidgeten.',
            'admin_name_placeholder' => 'Support',
            'random_admin_names_enabled' => 'Använd slumpmässiga administratörsnamn',
            'random_admin_names_enabled_help' => 'När aktiverat kommer varje ny konversation att tilldelas ett slumpmässigt administratörsnamn från listan nedan.',
            'random_admin_names' => 'Slumpmässiga administratörsnamn',
            'random_admin_names_help' => 'Ange ett namn per rad. Ett slumpmässigt namn kommer att tilldelas varje ny konversation.',
            'random_admin_names_placeholder' => 'Sarah
Mike
Emma
John',
            'primary_color' => 'Primärfärg',
            'primary_color_help' => 'Huvudfärgen för chatt-widgetens knapp och header. Välj en färg som passar ditt varumärke.',
            'position' => 'Widgetposition',
            'position_help' => 'Var chatt-widgetens knapp visas på skärmen.',
            'position_right' => 'Nederst till höger',
            'position_left' => 'Nederst till vänster',
            'email_required' => 'Kräv e-post',
            'email_required_help' => 'När den är aktiverad måste besökare ange sin e-postadress innan de startar en chatt. Användbart för uppföljande kommunikation.',
            'display_fields' => 'Visa fält',
            'mandatory_fields' => 'Obligatoriska fält',
            'poll_interval' => 'Pollningsintervall (ms)',
            'poll_interval_help' => 'Hur ofta nya meddelanden kontrolleras i millisekunder. Lägre värden betyder snabbare uppdateringar men fler serverförfrågningar. Rekommenderat: 3000-5000ms.',
            'poll_interval_placeholder' => '3000',
            'online_status' => 'Online-status',
            'working_hours_enabled' => 'Aktivera arbetstider',
            'working_hours_enabled_help' => 'När aktiverat, kommer chattstatusen automatiskt att växla till offline utanför arbetstid. Arbetstider baseras på tidszonen som konfigurerats i Inställningar → Allmänt.',
            'working_hours_start' => 'Starttid',
            'working_hours_end' => 'Sluttid',
            'working_days' => 'Arbetsdagar',
            'avatar_image' => 'Avatar-bild',
            'avatar_image_help' => 'Anpassad avatar-bild för chat-rubriken. Lämna tomt för att använda standardikonen.',
            'primary_hover_color' => 'Hovringsfärg',
            'primary_hover_color_help' => 'Färgen när du för musen över chattknappen.',
            'position_bottom_right' => 'Nere till höger',
            'position_bottom_left' => 'Nere till vänster',
            'position_center_right' => 'Mitten till höger',
            'position_center_left' => 'Mitten till vänster',
            'offset_x' => 'Offset X (px)',
            'offset_x_help' => 'Horisontellt avstånd från skärmkanten i pixlar.',
            'offset_y' => 'Offset Y (px)',
            'offset_y_help' => 'Vertikalt avstånd från skärmkanten i pixlar.',
            'display_on_mobile' => 'Visa på mobil',
            'display_on_mobile_help' => 'Styr om chatt-widgeten visas på mobila enheter.',
            'display_on_mobile_always' => 'Visa alltid',
            'display_on_mobile_hide' => 'Dölj på mobil',
        ],
    ],
    'email' => [
        'title' => 'Live Chat Email',
        'description' => 'Email notifications for live chat events',
        'templates' => [
            'new_conversation_title' => 'New Conversation Notification',
            'new_conversation_description' => 'Email sent to admin when a visitor starts a new conversation',
            'new_conversation_subject' => 'New Live Chat Conversation from {{ visitor_name }}',
            'visitor_name' => 'Visitor name',
            'visitor_email' => 'Visitor email',
            'visitor_phone' => 'Visitor phone',
            'visitor_ip' => 'Visitor IP address',
            'current_page' => 'Current page URL',
            'conversation_url' => 'Link to conversation in admin panel',
        ],
    ],
    'email_settings' => [
        'title' => 'Email Notifications',
        'description' => 'Configure email notifications for new chat conversations. Only one email is sent when a visitor starts a new conversation.',
        'enable_email_notification' => 'Enable Email Notifications',
        'enable_email_notification_help' => 'Send an email notification when a visitor starts a new chat conversation',
        'notification_emails' => 'Notification Email Addresses',
        'notification_emails_help' => 'Comma-separated list of email addresses to receive notifications. Leave empty to use the default admin email.',
    ],
    'email_templates' => [
        'new_conversation_title' => 'New Live Chat Conversation',
        'new_conversation_greeting' => 'A visitor has started a new live chat conversation on your website.',
        'visitor_details' => 'Visitor Details',
        'field_name' => 'Name:',
        'field_email' => 'Email:',
        'field_phone' => 'Phone:',
        'field_ip' => 'IP Address:',
        'field_page' => 'Current Page:',
        'respond_instruction' => 'Click the button below to respond to this conversation.',
        'view_conversation_button' => 'View Conversation',
    ],
    'webhook' => [
        'title' => 'Webhook-inställningar',
        'description' => 'Konfigurera webhooks för att ta emot aviseringar när chatthändelser inträffar. Webhooks skickar HTTP POST-förfrågningar till dina angivna URL:er med händelsedata.',
        'enable_webhooks' => 'Aktivera Webhooks',
        'enable_webhooks_help' => 'Aktivera sändning av webhook-aviseringar för livechatthändelser',
        'message_received_url' => 'Meddelande mottaget Webhook-URL',
        'message_received_url_help' => 'Utlöses när en besökare skickar ett nytt meddelande i chatten',
        'conversation_started_url' => 'Konversation startad Webhook-URL',
        'conversation_started_url_help' => 'Utlöses när en besökare startar en ny konversation',
        'view_sample_data' => 'Visa exempeldata',
        'test_button' => 'Skicka test-webhook',
        'testing' => 'Skickar...',
        'test_success' => 'Webhook skickad!',
        'test_failed' => 'Webhook-testet misslyckades.',
        'status_code' => 'Statuskod',
        'please_enter_url' => 'Ange en webhook-URL först',
        'error_occurred' => 'Ett fel uppstod vid testning av webhook',
        'usage_instructions_title' => 'How to Use Webhooks',
        'usage_instructions' => '<p><strong>Webhooks</strong> allow you to receive real-time notifications when events occur in Live Chat. When an event is triggered, an HTTP POST request is sent to your configured URL with JSON payload.</p>
<h6>HTTP Headers</h6>
<ul>
<li><code>Content-Type: application/json</code></li>
<li><code>X-Webhook-Event</code> - Event type (e.g., <code>message.received</code>, <code>conversation.started</code>)</li>
<li><code>X-Webhook-Timestamp</code> - ISO 8601 timestamp when webhook was sent</li>
<li><code>X-Webhook-Test: true</code> - Only present for test webhooks</li>
</ul>
<h6>Your Endpoint Requirements</h6>
<ul>
<li>Must accept HTTP POST requests</li>
<li>Must respond within 10 seconds (timeout limit)</li>
<li>Should return HTTP 2xx status code for success</li>
<li>Must be publicly accessible (HTTPS recommended)</li>
</ul>
<h6>Integration Tutorials</h6>
<ul>
<li><strong>Slack:</strong> <a href="https://api.slack.com/messaging/webhooks" target="_blank">Incoming Webhooks Guide</a></li>
<li><strong>Discord:</strong> <a href="https://support.discord.com/hc/en-us/articles/228383668-Intro-to-Webhooks" target="_blank">Intro to Webhooks</a></li>
<li><strong>Telegram:</strong> <a href="https://core.telegram.org/bots/webhooks" target="_blank">Bot Webhooks</a> (use with <a href="https://core.telegram.org/bots/api#sendmessage" target="_blank">sendMessage API</a>)</li>
<li><strong>n8n:</strong> <a href="https://docs.n8n.io/integrations/builtin/core-nodes/n8n-nodes-base.webhook/" target="_blank">Webhook Node</a></li>
<li><strong>Zapier:</strong> <a href="https://zapier.com/apps/webhook/integrations" target="_blank">Webhooks by Zapier</a></li>
<li><strong>Make (Integromat):</strong> <a href="https://www.make.com/en/help/tools/webhooks" target="_blank">Webhooks Module</a></li>
</ul>
<h6>Example Use Cases</h6>
<ul>
<li>Send notifications to Slack, Discord, or Telegram</li>
<li>Create tickets in helpdesk systems (Zendesk, Freshdesk)</li>
<li>Log conversations to external CRM</li>
<li>Trigger automated responses via n8n or Zapier</li>
</ul>',
    ],
    'offline' => 'Offline',
    'days' => [
        'monday' => 'Måndag',
        'tuesday' => 'Tisdag',
        'wednesday' => 'Onsdag',
        'thursday' => 'Torsdag',
        'friday' => 'Fredag',
        'saturday' => 'Lördag',
        'sunday' => 'Söndag',
    ],
];
