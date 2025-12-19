<?php

namespace FriendsOfBotble\LiveChat\Http\Requests\Fronts;

use Botble\Base\Rules\PhoneNumberRule;
use Botble\Support\Http\Requests\Request;
use FriendsOfBotble\LiveChat\Support\LiveChatHelper;

class StartChatRequest extends Request
{
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:120'],
        ];

        if (LiveChatHelper::isEmailEnabled()) {
            if (LiveChatHelper::isEmailRequired()) {
                $rules['email'] = ['required', 'email', 'max:120'];
            } else {
                $rules['email'] = ['nullable', 'email', 'max:120'];
            }
        } else {
            $rules['email'] = ['nullable'];
        }

        if (LiveChatHelper::isPhoneEnabled()) {
            if (LiveChatHelper::isPhoneRequired()) {
                $rules['phone'] = ['required', new PhoneNumberRule()];
            } else {
                $rules['phone'] = ['nullable', new PhoneNumberRule()];
            }
        } else {
            $rules['phone'] = ['nullable'];
        }

        $rules['current_url'] = ['nullable', 'string', 'max:2048'];

        return $rules;
    }
}
