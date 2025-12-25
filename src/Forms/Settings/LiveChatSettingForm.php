<?php

namespace FriendsOfBotble\LiveChat\Forms\Settings;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\RadioFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\RadioField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use FriendsOfBotble\LiveChat\Http\Requests\Settings\LiveChatSettingRequest;
use FriendsOfBotble\LiveChat\Services\WebhookService;
use FriendsOfBotble\LiveChat\Support\LiveChatHelper;

class LiveChatSettingForm extends SettingForm
{
    public function setup(): void
    {
        parent::setup();

        $this
            ->setValidatorClass(LiveChatSettingRequest::class)
            ->setSectionTitle(trans('plugins/fob-live-chat::live-chat.settings.title'))
            ->setSectionDescription(trans('plugins/fob-live-chat::live-chat.settings.description'))
            ->add(
                'fob_live_chat_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.enabled'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.enabled_help'))
                    ->value(LiveChatHelper::isEnabled())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_widget_title',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.widget_title'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.widget_title_help'))
                    ->placeholder(trans('plugins/fob-live-chat::live-chat.settings.form.widget_title_placeholder'))
                    ->value(setting('fob_live_chat_widget_title', trans('plugins/fob-live-chat::live-chat.chat_title')))
                    ->toArray()
            )
            ->add(
                'fob_live_chat_is_online',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.online_status'))
                    ->choices([
                        '1' => trans('plugins/fob-live-chat::live-chat.online'),
                        '0' => trans('plugins/fob-live-chat::live-chat.offline'),
                    ])
                    ->selected(setting('fob_live_chat_is_online', '1'))
                    ->toArray()
            )
            ->add(
                'fob_live_chat_working_hours_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.working_hours_enabled'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.working_hours_enabled_help'))
                    ->value($workingHoursEnabled = LiveChatHelper::isWorkingHoursEnabled())
                    ->toArray()
            )
            ->addOpenCollapsible('fob_live_chat_working_hours_enabled', '1', $workingHoursEnabled)
            ->add(
                'fob_live_chat_working_hours_start',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.working_hours_start'))
                    ->value(LiveChatHelper::getWorkingHoursStart())
                    ->attributes(['type' => 'time'])
                    ->toArray()
            )
            ->add(
                'fob_live_chat_working_hours_end',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.working_hours_end'))
                    ->value(LiveChatHelper::getWorkingHoursEnd())
                    ->attributes(['type' => 'time'])
                    ->toArray()
            )
            ->add(
                'fob_live_chat_working_days[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.working_days'))
                    ->choices([
                        '1' => trans('plugins/fob-live-chat::live-chat.days.monday'),
                        '2' => trans('plugins/fob-live-chat::live-chat.days.tuesday'),
                        '3' => trans('plugins/fob-live-chat::live-chat.days.wednesday'),
                        '4' => trans('plugins/fob-live-chat::live-chat.days.thursday'),
                        '5' => trans('plugins/fob-live-chat::live-chat.days.friday'),
                        '6' => trans('plugins/fob-live-chat::live-chat.days.saturday'),
                        '7' => trans('plugins/fob-live-chat::live-chat.days.sunday'),
                    ])
                    ->selected(LiveChatHelper::getWorkingDays())
                    ->toArray()
            )
            ->addCloseCollapsible('fob_live_chat_working_hours_enabled', '1')
            ->add(
                'fob_live_chat_avatar_image',
                MediaImageField::class,
                MediaImageFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.avatar_image'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.avatar_image_help'))
                    ->value(LiveChatHelper::getAvatarImage())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_welcome_message',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.welcome_message'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.welcome_message_help'))
                    ->placeholder(trans('plugins/fob-live-chat::live-chat.settings.form.welcome_message_placeholder'))
                    ->value(setting('fob_live_chat_welcome_message', ''))
                    ->toArray()
            )
            ->add(
                'fob_live_chat_admin_name',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.admin_name'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.admin_name_help'))
                    ->placeholder(trans('plugins/fob-live-chat::live-chat.settings.form.admin_name_placeholder'))
                    ->value(setting('fob_live_chat_admin_name', ''))
                    ->toArray()
            )
            ->add(
                'fob_live_chat_random_admin_names_enabled',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.random_admin_names_enabled'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.random_admin_names_enabled_help'))
                    ->value($randomNamesEnabled = setting('fob_live_chat_random_admin_names_enabled', false))
                    ->toArray()
            )
            ->addOpenCollapsible('fob_live_chat_random_admin_names_enabled', '1', $randomNamesEnabled == '1')
            ->add(
                'fob_live_chat_random_admin_names',
                TextareaField::class,
                TextareaFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.random_admin_names'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.random_admin_names_help'))
                    ->placeholder(trans('plugins/fob-live-chat::live-chat.settings.form.random_admin_names_placeholder'))
                    ->value(setting('fob_live_chat_random_admin_names', ''))
                    ->toArray()
            )
            ->addCloseCollapsible('fob_live_chat_random_admin_names_enabled', '1')
            ->add(
                'fob_live_chat_primary_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.primary_color'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.primary_color_help'))
                    ->value(LiveChatHelper::getPrimaryColor())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_primary_hover_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.primary_hover_color'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.primary_hover_color_help'))
                    ->value(LiveChatHelper::getPrimaryHoverColor())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_position',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.position'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.position_help'))
                    ->choices([
                        'bottom_right' => trans('plugins/fob-live-chat::live-chat.settings.form.position_bottom_right'),
                        'bottom_left' => trans('plugins/fob-live-chat::live-chat.settings.form.position_bottom_left'),
                        'center_right' => trans('plugins/fob-live-chat::live-chat.settings.form.position_center_right'),
                        'center_left' => trans('plugins/fob-live-chat::live-chat.settings.form.position_center_left'),
                    ])
                    ->selected(LiveChatHelper::getPosition())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_offset_x',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.offset_x'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.offset_x_help'))
                    ->value(LiveChatHelper::getOffsetX())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_offset_y',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.offset_y'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.offset_y_help'))
                    ->value(LiveChatHelper::getOffsetY())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_display_on_mobile',
                RadioField::class,
                RadioFieldOption::make()
                    ->choices([
                        'always' => trans('plugins/fob-live-chat::live-chat.settings.form.display_on_mobile_always'),
                        'hide' => trans('plugins/fob-live-chat::live-chat.settings.form.display_on_mobile_hide'),
                    ])
                    ->selected(LiveChatHelper::getDisplayOnMobile())
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.display_on_mobile'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.display_on_mobile_help'))
                    ->toArray()
            )
            ->add(
                'fob_live_chat_display_fields[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.display_fields'))
                    ->choices([
                        'email' => trans('core/base::forms.email'),
                        'phone' => trans('core/base::tables.phone'),
                    ])
                    ->selected(LiveChatHelper::getDisplayFields())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_mandatory_fields[]',
                MultiCheckListField::class,
                MultiChecklistFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.mandatory_fields'))
                    ->choices([
                        'email' => trans('core/base::forms.email'),
                        'phone' => trans('core/base::tables.phone'),
                    ])
                    ->selected(LiveChatHelper::getMandatoryFields())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_poll_interval',
                NumberField::class,
                NumberFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.poll_interval'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.poll_interval_help'))
                    ->placeholder(trans('plugins/fob-live-chat::live-chat.settings.form.poll_interval_placeholder'))
                    ->value(LiveChatHelper::getPollInterval())
                    ->toArray()
            )

            ->add('email_section_divider', HtmlField::class, [
                'html' => '<hr class="my-4">',
            ])

            ->add('email_section_heading', HtmlField::class, [
                'html' => sprintf(
                    '<div class="alert alert-info d-flex align-items-center mb-4"><x-core::icon name="ti ti-mail" class="icon-lg me-2" /><div><h5 class="alert-heading mb-1">%s</h5><p class="mb-0">%s</p></div></div>',
                    trans('plugins/fob-live-chat::live-chat.email_settings.title'),
                    trans('plugins/fob-live-chat::live-chat.email_settings.description')
                ),
            ])

            ->add(
                'fob_live_chat_enable_email_notification',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.email_settings.enable_email_notification'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.email_settings.enable_email_notification_help'))
                    ->value($emailEnabled = setting('fob_live_chat_enable_email_notification', true))
                    ->toArray()
            )
            ->addOpenCollapsible('fob_live_chat_enable_email_notification', '1', $emailEnabled == '1')

            ->add(
                'fob_live_chat_notification_emails',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.email_settings.notification_emails'))
                    ->value(setting('fob_live_chat_notification_emails', ''))
                    ->placeholder('admin@example.com, support@example.com')
                    ->helperText(trans('plugins/fob-live-chat::live-chat.email_settings.notification_emails_help'))
                    ->toArray()
            )
            ->addCloseCollapsible('fob_live_chat_enable_email_notification', '1')

            ->add('webhook_section_divider', HtmlField::class, [
                'html' => '<hr class="my-4">',
            ])

            ->add('webhook_section_heading', HtmlField::class, [
                'html' => sprintf(
                    '<div class="alert alert-secondary d-flex align-items-center mb-4"><x-core::icon name="ti ti-webhook" class="icon-lg me-2" /><div><h5 class="alert-heading mb-1">%s</h5><p class="mb-0">%s</p></div></div>',
                    trans('plugins/fob-live-chat::live-chat.webhook.title'),
                    trans('plugins/fob-live-chat::live-chat.webhook.description')
                ),
            ])

            ->add(
                'fob_live_chat_enable_webhooks',
                OnOffCheckboxField::class,
                OnOffFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.webhook.enable_webhooks'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.webhook.enable_webhooks_help'))
                    ->value($webhooksEnabled = setting('fob_live_chat_enable_webhooks', false))
                    ->toArray()
            )
            ->addOpenCollapsible('fob_live_chat_enable_webhooks', '1', $webhooksEnabled == '1')

            ->add(
                'fob_live_chat_message_received_webhook_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.webhook.message_received_url'))
                    ->value(setting('fob_live_chat_message_received_webhook_url'))
                    ->placeholder('https://your-webhook-endpoint.com/message-received')
                    ->helperText(trans('plugins/fob-live-chat::live-chat.webhook.message_received_url_help'))
                    ->toArray()
            )
            ->add('message_received_sample_data', HtmlField::class, [
                'html' => $this->getWebhookSampleDataHtml('message_received', $this->getMessageReceivedSampleData()),
            ])

            ->add(
                'fob_live_chat_conversation_started_webhook_url',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.webhook.conversation_started_url'))
                    ->value(setting('fob_live_chat_conversation_started_webhook_url'))
                    ->placeholder('https://your-webhook-endpoint.com/conversation-started')
                    ->helperText(trans('plugins/fob-live-chat::live-chat.webhook.conversation_started_url_help'))
                    ->toArray()
            )
            ->add('conversation_started_sample_data', HtmlField::class, [
                'html' => $this->getWebhookSampleDataHtml('conversation_started', $this->getConversationStartedSampleData()),
            ])

            ->add('webhook_test_script', HtmlField::class, [
                'html' => $this->getWebhookTestScript(),
            ])
            ->addCloseCollapsible('fob_live_chat_enable_webhooks', '1');
    }

    protected function getWebhookSampleDataHtml(string $webhookType, array $data): string
    {
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $title = trans('plugins/fob-live-chat::live-chat.webhook.view_sample_data');
        $testUrl = route('fob-live-chat.settings.webhook.test');
        $buttonText = trans('plugins/fob-live-chat::live-chat.webhook.test_button');
        $icon = BaseHelper::renderIcon('ti ti-send-2');

        return <<<HTML
            <div class="mb-3">
                <details class="border rounded p-3">
                    <summary class="cursor-pointer text-primary fw-bold">{$title}</summary>
                    <pre class="mt-3 p-3 rounded" style="max-height: 400px; overflow-y: auto;"><code>{$json}</code></pre>
                </details>
            </div>
            <div class="mb-3">
                <button type="button"
                        class="btn btn-sm btn-primary test-webhook-btn"
                        data-webhook-type="{$webhookType}"
                        data-url-field="fob_live_chat_{$webhookType}_webhook_url"
                        data-test-url="{$testUrl}">
                    {$icon} {$buttonText}
                </button>
                <div class="webhook-test-result mt-2" id="test-result-{$webhookType}" style="display: none;"></div>
            </div>
        HTML;
    }

    protected function getMessageReceivedSampleData(): array
    {
        return [
            'event' => WebhookService::EVENT_MESSAGE_RECEIVED,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'message_id' => 456,
                'content' => 'Hello, I need help with my order.',
                'is_from_admin' => false,
                'admin_name' => null,
                'conversation_id' => 123,
                'visitor_name' => 'John Doe',
                'visitor_email' => 'john@example.com',
                'visitor_phone' => '+1234567890',
                'visitor_ip' => '192.168.1.100',
                'current_url' => 'https://example.com/products/item-1',
                'status' => 'open',
                'created_at' => now()->toIso8601String(),
            ],
        ];
    }

    protected function getConversationStartedSampleData(): array
    {
        return [
            'event' => WebhookService::EVENT_CONVERSATION_STARTED,
            'timestamp' => now()->toIso8601String(),
            'data' => [
                'conversation_id' => 123,
                'visitor_name' => 'John Doe',
                'visitor_email' => 'john@example.com',
                'visitor_phone' => '+1234567890',
                'visitor_ip' => '192.168.1.100',
                'current_url' => 'https://example.com/products/item-1',
                'status' => 'open',
                'created_at' => now()->toIso8601String(),
            ],
        ];
    }

    protected function getWebhookTestScript(): string
    {
        $pleaseEnterUrlText = trans('plugins/fob-live-chat::live-chat.webhook.please_enter_url');
        $testingText = trans('plugins/fob-live-chat::live-chat.webhook.testing');
        $testFailedText = trans('plugins/fob-live-chat::live-chat.webhook.test_failed');
        $testSuccessText = trans('plugins/fob-live-chat::live-chat.webhook.test_success');
        $statusCodeText = trans('plugins/fob-live-chat::live-chat.webhook.status_code');
        $errorOccurredText = trans('plugins/fob-live-chat::live-chat.webhook.error_occurred');

        $loaderIcon = '<span style="display: inline-block; animation: spin 1s linear infinite;">' . BaseHelper::renderIcon('ti ti-loader-2', attributes: ['class' => 'm-0']) . '</span>';
        $loaderIcon = json_encode($loaderIcon);

        return <<<HTML
            <style>
                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }
            </style>
            <script>
                $(document).ready(function() {
                    const loaderIcon = {$loaderIcon};

                    $('.test-webhook-btn').on('click', function() {
                        const button = $(this);
                        const webhookType = button.data('webhook-type');
                        const urlFieldId = button.data('url-field');
                        const testUrl = button.data('test-url');
                        const webhookUrl = $('#' + urlFieldId).val();
                        const resultDiv = $('#test-result-' + webhookType);
                        const originalButtonHtml = button.html();

                        if (!webhookUrl) {
                            Botble.showError('{$pleaseEnterUrlText}');
                            return;
                        }

                        button.prop('disabled', true);
                        button.html(loaderIcon + ' {$testingText}');
                        resultDiv.hide();

                        $.ajax({
                            url: testUrl,
                            type: 'POST',
                            data: {
                                webhook_url: webhookUrl,
                                webhook_type: webhookType,
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.error) {
                                    resultDiv.html('<div class="alert alert-danger">' +
                                        '<p><strong>{$testFailedText}</strong> <br>' + response.message +
                                        (response.data ? '<br>{$statusCodeText}: ' + response.data.status_code : '') +
                                        '</p> </div>');
                                } else {
                                    resultDiv.html('<div class="alert alert-success">' +
                                        '<strong>{$testSuccessText}</strong> ' + response.message +
                                        '<br>{$statusCodeText}: ' + response.data.status_code +
                                        '</div>');
                                }
                                resultDiv.slideDown();
                            },
                            error: function(xhr) {
                                let errorMessage = '{$errorOccurredText}';
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMessage = xhr.responseJSON.message;
                                }
                                resultDiv.html('<div class="alert alert-danger">' +
                                    '<strong>{$testFailedText}</strong> ' + errorMessage +
                                    '</div>');
                                resultDiv.slideDown();
                            },
                            complete: function() {
                                button.prop('disabled', false);
                                button.html(originalButtonHtml);
                            }
                        });
                    });
                });
            </script>
        HTML;
    }
}
