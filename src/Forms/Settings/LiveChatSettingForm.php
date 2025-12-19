<?php

namespace FriendsOfBotble\LiveChat\Forms\Settings;

use Botble\Base\Forms\FieldOptions\ColorFieldOption;
use Botble\Base\Forms\FieldOptions\MultiChecklistFieldOption;
use Botble\Base\Forms\FieldOptions\NumberFieldOption;
use Botble\Base\Forms\FieldOptions\OnOffFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\TextareaFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\ColorField;
use Botble\Base\Forms\Fields\MultiCheckListField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffCheckboxField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Setting\Forms\SettingForm;
use FriendsOfBotble\LiveChat\Http\Requests\Settings\LiveChatSettingRequest;
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
                'fob_live_chat_primary_color',
                ColorField::class,
                ColorFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.primary_color'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.primary_color_help'))
                    ->value(LiveChatHelper::getPrimaryColor())
                    ->toArray()
            )
            ->add(
                'fob_live_chat_position',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/fob-live-chat::live-chat.settings.form.position'))
                    ->helperText(trans('plugins/fob-live-chat::live-chat.settings.form.position_help'))
                    ->choices([
                        'right' => trans('plugins/fob-live-chat::live-chat.settings.form.position_right'),
                        'left' => trans('plugins/fob-live-chat::live-chat.settings.form.position_left'),
                    ])
                    ->selected(LiveChatHelper::getPosition())
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
            );
    }
}
