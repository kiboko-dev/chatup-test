<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Knuckles\Scribe\Attributes as SA;
use Symfony\Component\HttpFoundation\Response;

#[
    SA\Group('V1'),
    SA\Subgroup('Пользователи')
]
class UserController extends Controller
{
    use Responses;

    #[SA\Endpoint(
        title: 'Список пользователей'
    ),
        SA\ResponseFromApiResource(
            name: UserResource::class,
            model: User::class,
            status: Response::HTTP_OK,
            collection: true,
        ),
    ]
    public function index(): JsonResponse
    {
        return $this->successResponseWithData(UserResource::collection(User::all()));
    }
}
