<?php

namespace FriendsOfBotble\LiveChat\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use FriendsOfBotble\LiveChat\Forms\Settings\LiveChatSettingForm;
use FriendsOfBotble\LiveChat\Http\Requests\Settings\LiveChatSettingRequest;

class LiveChatSettingController extends SettingController
{
    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-live-chat::live-chat.settings.title'));

        return LiveChatSettingForm::create()->renderForm();
    }

    public function update(LiveChatSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }
}
