<?php

namespace FriendsOfBotble\LiveChat\Http\Requests\Fronts;

use Botble\Support\Http\Requests\Request;

class SendMessageRequest extends Request
{
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
