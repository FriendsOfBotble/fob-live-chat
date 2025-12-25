<?php

namespace FriendsOfBotble\LiveChat\Support;

class LiveChatHelper
{
    /**
     * @return array<int, string>
     */
    public static function getDisplayFields(): array
    {
        $hasDisplayFields = setting()->has('fob_live_chat_display_fields');
        $displayFields = setting('fob_live_chat_display_fields');

        if (is_string($displayFields)) {
            $displayFields = json_decode($displayFields, true);
        }

        if (! is_array($displayFields)) {
            $displayFields = [];
        }

        if (! $hasDisplayFields) {
            $displayFields = ['email', 'phone'];
        }

        return array_values(array_unique($displayFields));
    }

    /**
     * @return array<int, string>
     */
    public static function getMandatoryFields(): array
    {
        $hasMandatoryFields = setting()->has('fob_live_chat_mandatory_fields');
        $mandatoryFields = setting('fob_live_chat_mandatory_fields');

        if (is_string($mandatoryFields)) {
            $mandatoryFields = json_decode($mandatoryFields, true);
        }

        if (! is_array($mandatoryFields)) {
            $mandatoryFields = [];
        }

        if (! $hasMandatoryFields && setting('fob_live_chat_email_required', false)) {
            $mandatoryFields = ['email'];
        }

        return array_values(array_intersect($mandatoryFields, self::getDisplayFields()));
    }

    public static function isEnabled(): bool
    {
        return (bool) setting('fob_live_chat_enabled', true);
    }

    public static function getWidgetTitle(): string
    {
        return setting('fob_live_chat_widget_title') ?: trans('plugins/fob-live-chat::live-chat.chat_title');
    }

    public static function isOnline(): bool
    {
        $manualStatus = (bool) setting('fob_live_chat_is_online', true);

        if (! $manualStatus) {
            return false;
        }

        if (! self::isWorkingHoursEnabled()) {
            return true;
        }

        return self::isWithinWorkingHours();
    }

    public static function isWorkingHoursEnabled(): bool
    {
        return (bool) setting('fob_live_chat_working_hours_enabled', false);
    }

    public static function getWorkingHoursStart(): string
    {
        return setting('fob_live_chat_working_hours_start', '09:00');
    }

    public static function getWorkingHoursEnd(): string
    {
        return setting('fob_live_chat_working_hours_end', '17:00');
    }

    public static function getWorkingDays(): array
    {
        $days = setting('fob_live_chat_working_days');

        if (is_string($days)) {
            $days = json_decode($days, true);
        }

        if (! is_array($days)) {
            return ['1', '2', '3', '4', '5'];
        }

        return $days;
    }

    public static function isWithinWorkingHours(): bool
    {
        $now = now();
        $currentDay = (string) $now->dayOfWeekIso;
        $workingDays = self::getWorkingDays();

        if (! in_array($currentDay, $workingDays, true)) {
            return false;
        }

        $startTime = self::getWorkingHoursStart();
        $endTime = self::getWorkingHoursEnd();
        $currentTime = $now->format('H:i');

        return $currentTime >= $startTime && $currentTime <= $endTime;
    }

    public static function getCurrentStatusText(): string
    {
        return self::isOnline()
            ? trans('plugins/fob-live-chat::live-chat.online')
            : trans('plugins/fob-live-chat::live-chat.offline');
    }

    public static function getCurrentStatusColor(): string
    {
        return self::isOnline() ? '#22c55e' : '#9ca3af';
    }

    public static function getAvatarImage(): ?string
    {
        return setting('fob_live_chat_avatar_image');
    }

    public static function getPollInterval(): int
    {
        return (int) setting('fob_live_chat_poll_interval', 3000);
    }

    public static function getPosition(): string
    {
        return setting('fob_live_chat_position', 'bottom_right');
    }

    public static function getOffsetX(): int
    {
        return (int) setting('fob_live_chat_offset_x', 20);
    }

    public static function getOffsetY(): int
    {
        return (int) setting('fob_live_chat_offset_y', 20);
    }

    public static function getDisplayOnMobile(): string
    {
        return setting('fob_live_chat_display_on_mobile', 'always');
    }

    public static function isHiddenOnMobile(): bool
    {
        return self::getDisplayOnMobile() === 'hide';
    }

    public static function getPrimaryColor(): string
    {
        return setting('fob_live_chat_primary_color', '#5A5EB9');
    }

    public static function getPrimaryHoverColor(): string
    {
        return setting('fob_live_chat_primary_hover_color', '#4a4ea0');
    }

    public static function getWelcomeMessage(): string
    {
        return setting('fob_live_chat_welcome_message') ?: trans('plugins/fob-live-chat::live-chat.default_welcome_message');
    }

    public static function isEmailEnabled(): bool
    {
        return in_array('email', self::getDisplayFields(), true);
    }

    public static function isPhoneEnabled(): bool
    {
        return in_array('phone', self::getDisplayFields(), true);
    }

    public static function isEmailRequired(): bool
    {
        return in_array('email', self::getMandatoryFields(), true);
    }

    public static function isPhoneRequired(): bool
    {
        return in_array('phone', self::getMandatoryFields(), true);
    }

    public static function getAdminName(): string
    {
        return setting('fob_live_chat_admin_name', trans('plugins/fob-live-chat::live-chat.default_admin_name'));
    }

    public static function isRandomAdminNamesEnabled(): bool
    {
        return (bool) setting('fob_live_chat_random_admin_names_enabled', false);
    }

    /**
     * @return array<int, string>
     */
    public static function getRandomAdminNames(): array
    {
        $names = setting('fob_live_chat_random_admin_names', '');

        if (empty($names)) {
            return [];
        }

        return array_filter(
            array_map('trim', explode("\n", $names)),
            fn ($name) => ! empty($name)
        );
    }

    public static function generateRandomAdminName(): ?string
    {
        if (! self::isRandomAdminNamesEnabled()) {
            return null;
        }

        $names = self::getRandomAdminNames();

        if (empty($names)) {
            return null;
        }

        return $names[array_rand($names)];
    }
}
