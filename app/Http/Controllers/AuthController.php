<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Requests\User\UserLoginRequest;
use App\Http\Requests\User\UserRegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Knuckles\Scribe\Attributes as SA;
use Symfony\Component\HttpFoundation\Response;

#[
    SA\Group('V1'),
    SA\Subgroup('Авторизация')
]
class AuthController extends Controller
{
    use Responses;

    #[SA\Endpoint(
        title: 'Регистрация пользователя'
    ),
        SA\ResponseFromApiResource(
            name: UserResource::class,
            model: User::class,
            status: Response::HTTP_OK,
            description: 'Returns product list',
            collection: true,
        ),
    ]
    public function register(UserRegisterRequest $request): JsonResponse
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

    #[
        SA\Endpoint(
            title: 'Авторизация пользователя',
            description: 'Возвращает токен и авторизует пользователя'
        ),
        SA\Response(content: '{
    "data": [
        {
            "token": "12|2f4apy5GSa757nFKoAeKW4F1uBlyPV2DI5X3G0eEbf885ebd"
        }
    ]
}', status: Response::HTTP_OK, description: 'OK'),
        SA\Response(content: '', status: Response::HTTP_BAD_REQUEST, description: 'Bad request'),
        SA\Response(content: '', status: Response::HTTP_CONFLICT, description: 'Conflict'),
    ]
    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = (object)$request->validated();

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::whereEmail($data->email)->firstOrFail();

        if (!Hash::check($data->password, $user->password)) {
            $this->errorResponse('Wrong password', 403);
        }

        if (Auth::attempt($credentials)) {
            session()->start();
        }

        $token = $user->createToken('auth_token');

        auth()->user()->token = $token->plainTextToken;

        return $this->successResponseWithData([
            'token' => $token->plainTextToken,
        ]);
    }
}
