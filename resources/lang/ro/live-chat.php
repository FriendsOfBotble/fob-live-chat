<?php

return [
    'title' => 'Chat live',
    'messages' => 'Mesaje',
    'visitor_name' => 'Numele vizitatorului',
    'visitor_email' => 'Emailul vizitatorului',
    'visitor_phone' => 'Telefon',
    'visitor_info' => 'Informații despre vizitator',
    'ip_address' => 'Adresa IP',
    'current_page' => 'Pagina curentă',
    'browser' => 'Navigator',
    'last_message' => 'Ultimul mesaj',
    'close_conversation' => 'Închide',
    'delete_conversation' => 'Șterge conversația',
    'conversation_closed' => 'Conversația a fost închisă.',
    'conversation_deleted' => 'Conversația a fost ștearsă.',
    'conversation_is_closed' => 'Această conversație este închisă.',
    'conversation_with' => 'Conversație cu :name',
    'confirm_close' => 'Sigur doriți să închideți această conversație?',
    'confirm_delete' => 'Sigur doriți să ștergeți această conversație? Această acțiune nu poate fi anulată.',
    'type_reply' => 'Scrieți răspunsul...',
    'reply_sent' => 'Răspuns trimis cu succes.',
    'no_conversation' => 'Nu a fost găsită nicio conversație activă. Vă rugăm să începeți un chat nou.',
    'no_conversations' => 'Nu există conversații încă',
    'chat_started' => 'Chat început cu succes.',
    'message_sent' => 'Mesaj trimis.',
    'select_conversation' => 'Selectați o conversație',
    'select_conversation_hint' => 'Alegeți o conversație din listă pentru a începe conversația.',
    'all' => 'Toate',
    'timeline' => 'Cronologie',
    'total_messages' => 'Total mesaje',
    'just_now' => 'Chiar acum',
    'test_sound' => 'Click pentru a testa sunetul',
    'back_to_list' => 'Înapoi la conversații',
    'no_messages_yet' => 'Niciun mesaj încă',
    'start_conversation_hint' => 'Trimiteți un mesaj pentru a începe conversația',
    'notifications_enabled' => 'Notificările browserului activate!',
    'enable_notifications' => 'Activează notificările browserului',
    'new_conversation' => 'Conversație nouă',
    'new_message' => 'Mesaj nou',
    'no_conversations_hint' => 'Conversațiile vor apărea aici când vizitatorii încep să discute.',
    'open_chat' => 'Deschide chatul',
    'close' => 'Închide',
    'chat_title' => 'Chat live',
    'online' => 'Conectat',
    'start_message' => 'Salut! Cum vă putem ajuta astăzi?',
    'your_name' => 'Numele dvs.',
    'your_email' => 'E-mailul dvs',
    'your_email_optional' => 'Emailul dvs. (opțional)',
    'your_phone' => 'Telefonul dvs',
    'start_chat' => 'Începe chatul',
    'type_message' => 'Scrieți un mesaj...',
    'send' => 'Trimite',
    'default_admin_name' => 'Suport',
    'default_welcome_message' => 'Salut! Mulțumim că ne-ați contactat. Cum vă pot ajuta astăzi?',
    'conversation_statuses' => [
        'open' => 'Deschis',
        'closed' => 'Închis',
    ],
    'settings' => [
        'title' => 'Chat live',
        'description' => 'Configurați widgetul de chat live plutitor care apare pe site-ul dvs. pentru suport clienți în timp real.',
        'form' => [
            'enabled' => 'Activează chat live',
            'enabled_help' => 'Activați pentru a afișa widgetul de chat pe site-ul dvs. Vizitatorii pot începe conversații cu echipa dvs. de suport.',
            'widget_title' => 'Titlul widgetului',
            'widget_title_help' => 'Titlul afișat în partea de sus a antetului widgetului de chat.',
            'widget_title_placeholder' => 'Chat live',
            'welcome_message' => 'Mesaj de bun venit',
            'welcome_message_help' => 'Primul mesaj pe care vizitatorii îl văd când deschid widgetul de chat. Lăsați gol pentru a folosi mesajul implicit.',
            'welcome_message_placeholder' => 'Salut! Mulțumim că ne-ați contactat. Cum vă pot ajuta astăzi?',
            'admin_name' => 'Nume administrator',
            'admin_name_help' => 'Numele afișat pentru răspunsurile administratorului în widgetul de chat.',
            'admin_name_placeholder' => 'Suport',
            'random_admin_names_enabled' => 'Utilizați nume de administrator aleatorii',
            'random_admin_names_enabled_help' => 'Când este activat, fiecărei noi conversații i se va atribui un nume de administrator aleatoriu din lista de mai jos.',
            'random_admin_names' => 'Nume de administrator aleatorii',
            'random_admin_names_help' => 'Introduceți un nume pe rând. Un nume aleatoriu va fi atribuit fiecărei noi conversații.',
            'random_admin_names_placeholder' => 'Sarah
Mike
Emma
John',
            'primary_color' => 'Culoare principală',
            'primary_color_help' => 'Culoarea principală pentru butonul și antetul widgetului de chat. Alegeți o culoare care să se potrivească brandului dvs.',
            'position' => 'Poziția widgetului',
            'position_help' => 'Unde apare butonul widgetului de chat pe ecran.',
            'position_right' => 'Dreapta jos',
            'position_left' => 'Stânga jos',
            'email_required' => 'Solicită email',
            'email_required_help' => 'Când este activat, vizitatorii trebuie să introducă adresa de email înainte de a începe un chat. Util pentru comunicări ulterioare.',
            'display_fields' => 'Câmpuri afișate',
            'mandatory_fields' => 'Câmpuri obligatorii',
            'poll_interval' => 'Interval de interogare (ms)',
            'poll_interval_help' => 'Cât de des să verificați mesajele noi în milisecunde. Valorile mai mici înseamnă actualizări mai rapide, dar mai multe solicitări către server. Recomandat: 3000-5000ms.',
            'poll_interval_placeholder' => '3000',
            'online_status' => 'Stare online',
            'working_hours_enabled' => 'Activează orele de lucru',
            'working_hours_enabled_help' => 'Când este activat, starea chat-ului va comuta automat la offline în afara orelor de lucru. Orele de lucru se bazează pe fusul orar configurat în Setări → General.',
            'working_hours_start' => 'Ora de începere',
            'working_hours_end' => 'Ora de sfârșit',
            'working_days' => 'Zile lucrătoare',
            'avatar_image' => 'Imagine avatar',
            'avatar_image_help' => 'Imagine de avatar personalizată pentru antetul chat-ului. Lăsați gol pentru a utiliza pictograma implicită.',
            'primary_hover_color' => 'Culoare la trecerea mouse-ului',
            'primary_hover_color_help' => 'Culoarea la trecerea mouse-ului peste butonul de chat.',
            'position_bottom_right' => 'Dreapta jos',
            'position_bottom_left' => 'Stânga jos',
            'position_center_right' => 'Dreapta centru',
            'position_center_left' => 'Stânga centru',
            'offset_x' => 'Decalaj X (px)',
            'offset_x_help' => 'Decalaj orizontal de la marginea ecranului în pixeli.',
            'offset_y' => 'Decalaj Y (px)',
            'offset_y_help' => 'Decalaj vertical de la marginea ecranului în pixeli.',
            'display_on_mobile' => 'Afișează pe mobil',
            'display_on_mobile_help' => 'Controlează dacă widget-ul de chat este afișat pe dispozitivele mobile.',
            'display_on_mobile_always' => 'Afișează întotdeauna',
            'display_on_mobile_hide' => 'Ascunde pe mobil',
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
        'title' => 'Setări Webhook',
        'description' => 'Configurați webhook-uri pentru a primi notificări când apar evenimente de chat. Webhook-urile trimit cereri HTTP POST la URL-urile specificate cu date despre evenimente.',
        'enable_webhooks' => 'Activare Webhook-uri',
        'enable_webhooks_help' => 'Activați trimiterea notificărilor webhook pentru evenimentele de chat live',
        'message_received_url' => 'URL Webhook mesaj primit',
        'message_received_url_help' => 'Se declanșează când un vizitator trimite un mesaj nou în chat',
        'conversation_started_url' => 'URL Webhook conversație începută',
        'conversation_started_url_help' => 'Se declanșează când un vizitator începe o nouă conversație',
        'view_sample_data' => 'Vizualizare date exemplu',
        'test_button' => 'Trimite Webhook de test',
        'testing' => 'Se trimite...',
        'test_success' => 'Webhook trimis cu succes!',
        'test_failed' => 'Testul webhook a eșuat.',
        'status_code' => 'Cod de stare',
        'please_enter_url' => 'Vă rugăm să introduceți mai întâi un URL webhook',
        'error_occurred' => 'A apărut o eroare la testarea webhook-ului',
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
        'monday' => 'Luni',
        'tuesday' => 'Marți',
        'wednesday' => 'Miercuri',
        'thursday' => 'Joi',
        'friday' => 'Vineri',
        'saturday' => 'Sâmbătă',
        'sunday' => 'Duminică',
    ],
];
