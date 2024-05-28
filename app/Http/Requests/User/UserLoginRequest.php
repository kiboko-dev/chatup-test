<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;

#[BodyParam('email', 'string', 'email пользователя', required: true, example: 'gabrielle@example.net')]
#[BodyParam('password', 'string', 'Пароль', required: true, example: 'sddsfwwe23234')]
class UserLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email|exists:users,email',
            'password' => 'required|string',
        ];
    }
}
