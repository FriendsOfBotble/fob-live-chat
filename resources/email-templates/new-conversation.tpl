{{ header }}

<div class="bb-main-content">
    <table class="bb-box" cellpadding="0" cellspacing="0">
        <tbody>
            <tr>
                <td class="bb-content bb-pb-0" align="center">
                    <table class="bb-icon bb-icon-lg bb-bg-blue" cellspacing="0" cellpadding="0">
                        <tbody>
                            <tr>
                                <td valign="middle" align="center">
                                    <img src="{{ 'message-circle' | icon_url }}" class="bb-va-middle" width="40" height="40" alt="Icon">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <h1 class="bb-text-center bb-m-0 bb-mt-md">{{ 'plugins/fob-live-chat::live-chat.email_templates.new_conversation_title' | trans }}</h1>
                </td>
            </tr>
            <tr>
                <td>
                    <table cellpadding="0" cellspacing="0">
                        <tbody>
                            <tr>
                                <td class="bb-content">
                                    <p>{{ 'plugins/fob-live-chat::live-chat.email_templates.new_conversation_greeting' | trans }}</p>

                                    <h4>{{ 'plugins/fob-live-chat::live-chat.email_templates.visitor_details' | trans }}</h4>

                                    <table class="bb-table" cellspacing="0" cellpadding="0">
                                        <thead>
                                            <tr>
                                                <th width="120px"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>{{ 'plugins/fob-live-chat::live-chat.email_templates.field_name' | trans }}</td>
                                                <td class="bb-font-strong bb-text-left">{{ visitor_name }}</td>
                                            </tr>
                                            {% if visitor_email and visitor_email != '-' %}
                                            <tr>
                                                <td>{{ 'plugins/fob-live-chat::live-chat.email_templates.field_email' | trans }}</td>
                                                <td class="bb-font-strong bb-text-left">{{ visitor_email }}</td>
                                            </tr>
                                            {% endif %}
                                            {% if visitor_phone and visitor_phone != '-' %}
                                            <tr>
                                                <td>{{ 'plugins/fob-live-chat::live-chat.email_templates.field_phone' | trans }}</td>
                                                <td class="bb-font-strong bb-text-left">{{ visitor_phone }}</td>
                                            </tr>
                                            {% endif %}
                                            {% if visitor_ip and visitor_ip != '-' %}
                                            <tr>
                                                <td>{{ 'plugins/fob-live-chat::live-chat.email_templates.field_ip' | trans }}</td>
                                                <td class="bb-font-strong bb-text-left">{{ visitor_ip }}</td>
                                            </tr>
                                            {% endif %}
                                            {% if current_page and current_page != '-' %}
                                            <tr>
                                                <td>{{ 'plugins/fob-live-chat::live-chat.email_templates.field_page' | trans }}</td>
                                                <td class="bb-font-strong bb-text-left"><a href="{{ current_page }}">{{ current_page }}</a></td>
                                            </tr>
                                            {% endif %}
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td class="bb-content bb-text-center bb-pt-0 bb-pb-xl" align="center">
                                    <p>{{ 'plugins/fob-live-chat::live-chat.email_templates.respond_instruction' | trans }}</p><br />
                                    <a href="{{ conversation_url }}" class="bb-btn bb-bg-blue bb-border-blue">{{ 'plugins/fob-live-chat::live-chat.email_templates.view_conversation_button' | trans }}</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{ footer }}
