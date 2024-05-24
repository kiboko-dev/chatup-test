<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => 'nullable|exists:users,id',
        ];
    }
}
