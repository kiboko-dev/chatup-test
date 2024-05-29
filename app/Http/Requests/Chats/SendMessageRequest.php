<?php

namespace App\Http\Requests\Chats;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('chat_id', 'integer', 'ID чата', required: true, example: 1)]
#[BodyParam('message', 'string', 'Сообщений', required: true, example: 'Какой-то текст')]

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'chat_id' => 'required|integer|exists:chats,id',
            'message' => 'required|string'
        ];
    }
}
