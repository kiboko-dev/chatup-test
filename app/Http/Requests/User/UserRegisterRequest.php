<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Knuckles\Scribe\Attributes\BodyParam;
use Knuckles\Scribe\Attributes\UrlParam;

#[BodyParam('email', 'string', 'email пользователя', required: true, example: "gabrielle@example.net")]
#[BodyParam('password', 'string', 'Пароль', required: true, example: "sddsfwwe23234")]
#[BodyParam('lastName', 'string', 'Фамилия пользователя', required: true, example: "Иванов")]
#[BodyParam('firstName', 'string', 'Имя пользователя', required: true, example: "Иван")]
class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => 'required|string|email:rfc|max:255|unique:users',
            'password' => 'required|string|min:6',
            'lastName' => 'required|string|min:2|max:255',
            'firstName' => 'required|string|min:2|max:255',
        ];
    }
}
