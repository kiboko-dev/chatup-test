<?php

namespace App\Http\Controllers;

use App\Helpers\Responses;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    use Responses;

    public function index(): JsonResponse
    {
        return $this->successResponseWithData(UserResource::collection(User::all()));
    }
}
