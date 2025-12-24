<?php

namespace FriendsOfBotble\LiveChat\Http\Controllers\Settings;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Setting\Http\Controllers\SettingController;
use FriendsOfBotble\LiveChat\Forms\Settings\LiveChatSettingForm;
use FriendsOfBotble\LiveChat\Http\Requests\Settings\LiveChatSettingRequest;
use FriendsOfBotble\LiveChat\Services\WebhookService;
use Illuminate\Http\Request;

class LiveChatSettingController extends SettingController
{
    public function __construct(protected WebhookService $webhookService)
    {
    }

    public function edit()
    {
        $this->pageTitle(trans('plugins/fob-live-chat::live-chat.settings.title'));

        return LiveChatSettingForm::create()->renderForm();
    }

    public function update(LiveChatSettingRequest $request): BaseHttpResponse
    {
        return $this->performUpdate($request->validated());
    }

    public function testWebhook(Request $request): BaseHttpResponse
    {
        $request->validate([
            'webhook_url' => ['required', 'url'],
            'webhook_type' => ['required', 'string'],
        ]);

        $result = $this->webhookService->testWebhook(
            $request->input('webhook_url'),
            $request->input('webhook_type')
        );

        return $this->httpResponse()
            ->setError(! $result['success'])
            ->setMessage($result['message'])
            ->setData(['status_code' => $result['status_code']]);
    }
}
