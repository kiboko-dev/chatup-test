<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use Responses;

    public function register(UserRegisterRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = (object)$request->validated();

        $user = User::firstOrCreate([
            'email' => $data->email,
        ], [
            'first_name' => $data->firstName,
            'last_name' => $data->lastName,
            'password' => Hash::make($data->password),
        ]);

        return $this->successResponseWithData(UserResource::make($user));
    }

    public function login(UserLoginRequest $request): \Illuminate\Http\JsonResponse
    {
        $data = (object)$request->validated();

        $user = User::whereEmail($data->email)->firstOrFail();
        $token = $user->createToken('auth_token');

        auth()->user()->token = $token->plainTextToken;

        return $this->successResponseWithData([
            'token' => $token->plainTextToken,
        ]);
    }
}
